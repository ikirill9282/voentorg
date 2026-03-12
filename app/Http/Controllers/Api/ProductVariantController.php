<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductVariantController extends Controller
{
    /**
     * Given a partial set of selected attribute value IDs,
     * return which values are available for each attribute.
     */
    public function available(Request $request, Product $product): JsonResponse
    {
        $selected = collect($request->input('selected', []))->map(fn($v) => (int) $v)->filter();

        $variants = $product->variants()
            ->active()
            ->with('attributeValues')
            ->get();

        // Build a map: attribute_id => [value_id, ...]  for each variant
        $variantMaps = $variants->map(function ($variant) {
            $map = [];
            foreach ($variant->attributeValues as $av) {
                $attrId = $av->pivot->product_attribute_id ?? $av->attribute_id;
                $map[$attrId] = $av->id;
            }
            return ['variant' => $variant, 'map' => $map];
        });

        // Determine which attribute each selected value belongs to
        $selectedByAttr = [];
        foreach ($variantMaps as $vm) {
            foreach ($vm['map'] as $attrId => $valId) {
                if ($selected->contains($valId)) {
                    $selectedByAttr[$attrId] = $valId;
                }
            }
        }

        // For each attribute, find which values are compatible with selections of OTHER attributes
        $available = [];
        $allAttrIds = $variantMaps->flatMap(fn($vm) => array_keys($vm['map']))->unique();

        foreach ($allAttrIds as $attrId) {
            $otherSelections = collect($selectedByAttr)->except($attrId);

            $availableValues = [];
            foreach ($variantMaps as $vm) {
                // Check if this variant matches all OTHER selected attributes
                $matches = true;
                foreach ($otherSelections as $otherAttrId => $otherValId) {
                    if (($vm['map'][$otherAttrId] ?? null) !== $otherValId) {
                        $matches = false;
                        break;
                    }
                }
                if ($matches && isset($vm['map'][$attrId])) {
                    $availableValues[] = $vm['map'][$attrId];
                }
            }
            $available[$attrId] = array_values(array_unique($availableValues));
        }

        // Also try to find exact match variant
        $matchedVariant = null;
        if (count($selectedByAttr) === $allAttrIds->count()) {
            $sortedSelected = collect($selectedByAttr)->values()->sort()->values();
            $matchedVariant = $variants->first(function ($variant) use ($sortedSelected) {
                $variantAttrIds = $variant->attributeValues->pluck('id')->sort()->values();
                return $variantAttrIds->toArray() === $sortedSelected->toArray();
            });
        }

        $result = ['available' => $available];

        if ($matchedVariant) {
            $result['found'] = true;
            $result['variant_id'] = $matchedVariant->id;
            $result['price'] = (float) $matchedVariant->price;
            $result['old_price'] = $matchedVariant->old_price ? (float) $matchedVariant->old_price : null;
            $result['price_formatted'] = number_format($matchedVariant->price, 0, '', ' ') . ' ₽';
            $result['old_price_formatted'] = $matchedVariant->old_price
                ? number_format($matchedVariant->old_price, 0, '', ' ') . ' ₽'
                : null;
            $result['sku'] = $matchedVariant->sku;
            $result['stock'] = (int) $matchedVariant->stock;
            $result['in_stock'] = $matchedVariant->stock > 0;
        } else {
            $result['found'] = false;
        }

        return response()->json($result);
    }

    public function show(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate([
            'attributes' => ['required', 'array'],
            'attributes.*' => ['required', 'integer', 'exists:product_attribute_values,id'],
        ]);

        $attributeValueIds = collect($validated['attributes'])->sort()->values();

        $variants = $product->variants()
            ->active()
            ->with('attributeValues')
            ->get();

        $matchedVariant = $variants->first(function ($variant) use ($attributeValueIds) {
            $variantAttrIds = $variant->attributeValues->pluck('id')->sort()->values();
            return $variantAttrIds->toArray() === $attributeValueIds->toArray();
        });

        if (! $matchedVariant) {
            return response()->json([
                'found' => false,
                'message' => 'Вариация не найдена',
            ]);
        }

        return response()->json([
            'found' => true,
            'variant_id' => $matchedVariant->id,
            'price' => (float) $matchedVariant->price,
            'old_price' => $matchedVariant->old_price ? (float) $matchedVariant->old_price : null,
            'price_formatted' => number_format($matchedVariant->price, 0, '', ' ') . ' ₽',
            'old_price_formatted' => $matchedVariant->old_price
                ? number_format($matchedVariant->old_price, 0, '', ' ') . ' ₽'
                : null,
            'sku' => $matchedVariant->sku,
            'stock' => (int) $matchedVariant->stock,
            'in_stock' => $matchedVariant->stock > 0,
        ]);
    }
}
