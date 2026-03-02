<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductVariantController extends Controller
{
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
