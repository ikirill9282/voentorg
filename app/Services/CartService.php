<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class CartService
{
    private const SESSION_KEY = 'cart.items';
    private const COUPON_KEY = 'cart.coupon';

    public function add(Product $product, int $quantity, ?int $variantId = null): void
    {
        $this->ensureCanUseProduct($product);

        if ($quantity < 1) {
            throw ValidationException::withMessages([
                'quantity' => 'Количество должно быть больше нуля.',
            ]);
        }

        $variant = null;
        if ($variantId) {
            $variant = $product->variants()->active()->with('attributeValues.attribute')->find($variantId);
            if (! $variant) {
                throw ValidationException::withMessages([
                    'variant_id' => 'Выбранная вариация недоступна.',
                ]);
            }
        }

        $stock = $variant ? (int) $variant->stock : (int) $product->stock;

        $items = $this->rawItems();
        $rowId = $variant ? "{$product->id}-{$variant->id}" : (string) $product->id;
        $existingQty = (int) ($items[$rowId]['quantity'] ?? 0);
        $newQty = $existingQty + $quantity;

        if ($newQty > $stock) {
            throw ValidationException::withMessages([
                'quantity' => 'Недостаточно товара на складе.',
            ]);
        }

        $items[$rowId] = $this->buildItemPayload($product, $newQty, $variant);
        $this->storeItems($items);
    }

    public function update(string $rowId, int $quantity): void
    {
        $items = $this->rawItems();

        if (! array_key_exists($rowId, $items)) {
            throw ValidationException::withMessages([
                'quantity' => 'Позиция корзины не найдена.',
            ]);
        }

        if ($quantity < 1) {
            throw ValidationException::withMessages([
                'quantity' => 'Количество должно быть больше нуля.',
            ]);
        }

        $productId = (int) $items[$rowId]['product_id'];
        $variantId = $items[$rowId]['variant_id'] ?? null;

        $product = Product::query()->with('images')->find($productId);

        if (! $product || ! $product->is_active) {
            unset($items[$rowId]);
            $this->storeItems($items);

            throw ValidationException::withMessages([
                'quantity' => 'Товар недоступен.',
            ]);
        }

        $variant = null;
        if ($variantId) {
            $variant = ProductVariant::query()->active()->with('attributeValues.attribute')->find($variantId);
        }

        $stock = $variant ? (int) $variant->stock : (int) $product->stock;

        if ($quantity > $stock) {
            throw ValidationException::withMessages([
                'quantity' => 'Недостаточно товара на складе.',
            ]);
        }

        $items[$rowId] = $this->buildItemPayload($product, $quantity, $variant);
        $this->storeItems($items);
    }

    public function remove(string $rowId): void
    {
        $items = $this->rawItems();
        unset($items[$rowId]);
        $this->storeItems($items);
    }

    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
        session()->forget(self::COUPON_KEY);
    }

    public function hasItems(): bool
    {
        return ! empty($this->rawItems());
    }

    public function applyCoupon(string $code, float $subtotal): Coupon
    {
        $coupon = Coupon::query()->where('code', $code)->first();

        if (! $coupon) {
            throw ValidationException::withMessages([
                'coupon_code' => 'Купон не найден.',
            ]);
        }

        if (! $coupon->isValid($subtotal)) {
            throw ValidationException::withMessages([
                'coupon_code' => 'Купон недействителен или не применим к данному заказу.',
            ]);
        }

        session()->put(self::COUPON_KEY, $coupon->id);

        return $coupon;
    }

    public function removeCoupon(): void
    {
        session()->forget(self::COUPON_KEY);
    }

    public function summary(): array
    {
        $rawItems = $this->rawItems();

        if (empty($rawItems)) {
            return [
                'items' => collect(),
                'subtotal' => 0,
                'discount' => 0,
                'total' => 0,
                'total_quantity' => 0,
                'is_empty' => true,
                'coupon' => null,
            ];
        }

        $productIds = array_unique(array_map(
            static fn ($row) => (int) $row['product_id'],
            array_values($rawItems)
        ));

        $products = Product::query()
            ->whereIn('id', $productIds)
            ->with('images')
            ->get()
            ->keyBy('id');

        $variantIds = array_filter(array_map(
            static fn ($row) => $row['variant_id'] ?? null,
            array_values($rawItems)
        ));

        $variants = ! empty($variantIds)
            ? ProductVariant::query()
                ->whereIn('id', $variantIds)
                ->with('attributeValues.attribute')
                ->get()
                ->keyBy('id')
            : collect();

        $items = collect();
        $updatedRawItems = [];

        foreach ($rawItems as $rowId => $item) {
            $product = $products->get((int) $item['product_id']);

            if (! $product || ! $product->is_active) {
                continue;
            }

            $variant = isset($item['variant_id']) ? $variants->get((int) $item['variant_id']) : null;

            if ($variant && ! $variant->is_active) {
                continue;
            }

            $stock = $variant ? (int) $variant->stock : (int) $product->stock;

            if ($stock < 1) {
                continue;
            }

            $quantity = min((int) $item['quantity'], $stock);
            if ($quantity < 1) {
                continue;
            }

            $payload = $this->buildItemPayload($product, $quantity, $variant);
            $updatedRawItems[$rowId] = $payload;

            $lineTotal = $quantity * (float) $payload['price'];
            $items->push([
                ...$payload,
                'row_id' => $rowId,
                'line_total' => $lineTotal,
            ]);
        }

        if ($updatedRawItems !== $rawItems) {
            $this->storeItems($updatedRawItems);
        }

        $subtotal = (float) $items->sum('line_total');

        // Coupon discount
        $discount = 0;
        $coupon = null;
        $couponId = session()->get(self::COUPON_KEY);
        if ($couponId) {
            $coupon = Coupon::with(['products', 'categories', 'freeProduct'])->find($couponId);
            if ($coupon && $coupon->isValid($subtotal)) {
                $applicableSubtotal = $this->calculateApplicableSubtotal($coupon, $items);
                $discount = $coupon->calculateDiscount($subtotal, $applicableSubtotal);

                // Add free product to cart items if applicable
                if ($coupon->type === 'free_product' && $coupon->freeProduct) {
                    $freeProduct = $coupon->freeProduct;
                    $items->push([
                        'product_id' => $freeProduct->id,
                        'variant_id' => null,
                        'name' => $freeProduct->name,
                        'slug' => $freeProduct->slug,
                        'sku' => $freeProduct->sku,
                        'price' => 0,
                        'quantity' => 1,
                        'image' => $freeProduct->primaryImagePath(),
                        'stock' => 1,
                        'variant_label' => null,
                        'variant_attributes' => [],
                        'size' => null,
                        'color' => null,
                        'category_id' => $freeProduct->category_id,
                        'row_id' => 'free_gift',
                        'line_total' => 0,
                        'is_free_gift' => true,
                    ]);
                }
            } else {
                session()->forget(self::COUPON_KEY);
                $coupon = null;
            }
        }

        $total = max(0, $subtotal - $discount);

        return [
            'items' => $items,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => $total,
            'total_quantity' => (int) $items->sum('quantity'),
            'is_empty' => $items->isEmpty(),
            'coupon' => $coupon,
        ];
    }

    public function itemsCollection(): Collection
    {
        return collect($this->summary()['items']);
    }

    private function ensureCanUseProduct(Product $product): void
    {
        if (! $product->is_active) {
            throw ValidationException::withMessages([
                'product_id' => 'Товар недоступен.',
            ]);
        }

        if ($product->stock < 1 && ! $product->hasVariants()) {
            throw ValidationException::withMessages([
                'product_id' => 'Товар закончился.',
            ]);
        }
    }

    private function buildItemPayload(Product $product, int $quantity, ?ProductVariant $variant = null): array
    {
        $price = $variant ? (float) $variant->price : (float) $product->price;
        $stock = $variant ? (int) $variant->stock : (int) $product->stock;
        $sku = $variant && $variant->sku ? $variant->sku : $product->sku;

        $variantLabel = null;
        $variantAttributes = [];
        $size = null;
        $color = null;

        if ($variant) {
            $parts = [];
            foreach ($variant->attributeValues as $attrVal) {
                $attrSlug = $attrVal->attribute->slug ?? '';
                $attrName = $attrVal->attribute->name;
                $parts[] = $attrName . ': ' . $attrVal->value;
                $variantAttributes[$attrName] = $attrVal->value;

                if (in_array($attrSlug, ['razmer', 'size'])) {
                    $size = $attrVal->value;
                }
                if (in_array($attrSlug, ['color', 'colour', 'tsvet'])) {
                    $color = $attrVal->value;
                }
            }
            $variantLabel = implode(', ', $parts);
        }

        return [
            'product_id' => $product->id,
            'variant_id' => $variant?->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'sku' => $sku,
            'price' => $price,
            'quantity' => $quantity,
            'image' => $product->primaryImagePath(),
            'stock' => $stock,
            'variant_label' => $variantLabel,
            'variant_attributes' => $variantAttributes,
            'size' => $size,
            'color' => $color,
            'category_id' => $product->category_id,
        ];
    }

    private function calculateApplicableSubtotal(Coupon $coupon, \Illuminate\Support\Collection $items): float
    {
        if ($coupon->scope === 'cart') {
            return (float) $items->sum('line_total');
        }

        if ($coupon->scope === 'products') {
            $productIds = $coupon->products->pluck('id')->all();

            return (float) $items->filter(
                fn ($item) => in_array((int) $item['product_id'], $productIds)
            )->sum('line_total');
        }

        if ($coupon->scope === 'categories') {
            $categoryIds = $coupon->categories->pluck('id')->all();

            return (float) $items->filter(
                fn ($item) => in_array((int) ($item['category_id'] ?? 0), $categoryIds)
            )->sum('line_total');
        }

        return (float) $items->sum('line_total');
    }

    private function rawItems(): array
    {
        return session()->get(self::SESSION_KEY, []);
    }

    private function storeItems(array $items): void
    {
        session()->put(self::SESSION_KEY, $items);
    }
}
