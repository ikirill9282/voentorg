<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'description',
        'type',
        'scope',
        'free_product_id',
        'value',
        'min_order_amount',
        'max_discount',
        'usage_limit',
        'used_count',
        'active_from',
        'active_until',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'is_active' => 'boolean',
        'active_from' => 'date',
        'active_until' => 'date',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function freeProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'free_product_id');
    }

    public function isValid(float $subtotal): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->active_from && now()->lt($this->active_from)) {
            return false;
        }

        if ($this->active_until && now()->gt($this->active_until->endOfDay())) {
            return false;
        }

        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return false;
        }

        if ($this->min_order_amount !== null && $subtotal < (float) $this->min_order_amount) {
            return false;
        }

        return true;
    }

    /**
     * @param float $subtotal        Full cart subtotal
     * @param float|null $applicable Subtotal of items the coupon applies to (for scoped coupons)
     */
    public function calculateDiscount(float $subtotal, ?float $applicable = null): float
    {
        $base = $applicable ?? $subtotal;

        if ($this->type === 'free_product') {
            $freeProduct = $this->relationLoaded('freeProduct')
                ? $this->freeProduct
                : $this->freeProduct()->first();

            if (! $freeProduct) {
                return 0;
            }

            return min((float) $freeProduct->price, $subtotal);
        }

        if ($this->type === 'percent') {
            $discount = round($base * (float) $this->value / 100, 2);

            if ($this->max_discount !== null) {
                $discount = min($discount, (float) $this->max_discount);
            }

            return min($discount, $subtotal);
        }

        // fixed
        return min((float) $this->value, $base, $subtotal);
    }
}
