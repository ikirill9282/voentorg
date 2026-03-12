<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    public const STATUS_NEW = 'new';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    public const PAYMENT_PENDING = 'pending';
    public const PAYMENT_PAID = 'paid';
    public const PAYMENT_FAILED = 'failed';

    protected $fillable = [
        'external_id',
        'external_status',
        'commerceml_exported_at',
        'order_number',
        'user_id',
        'customer_first_name',
        'customer_last_name',
        'customer_email',
        'customer_phone',
        'customer_address_line_1',
        'customer_address_line_2',
        'customer_city',
        'customer_region',
        'customer_postal_code',
        'customer_country',
        'shipping_method_id',
        'delivery_company_id',
        'delivery_region',
        'subtotal',
        'shipping_total',
        'total',
        'payment_method',
        'payment_status',
        'status',
        'comment',
        'cdek_uuid',
        'cdek_tracking_number',
        'discount_amount',
        'delivery_provider',
        'pickup_store_id',
        'pickup_prepaid',
        'pickup_estimated_days',
        'yandex_claim_id',
        'payment_id',
        'payment_url',
        'paid_at',
        'bonus_used',
        'bonus_earned',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'shipping_total' => 'decimal:2',
            'total' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'commerceml_exported_at' => 'datetime',
            'bonus_used' => 'decimal:2',
            'bonus_earned' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function shippingMethod(): BelongsTo
    {
        return $this->belongsTo(ShippingMethod::class);
    }

    public function deliveryCompany(): BelongsTo
    {
        return $this->belongsTo(DeliveryCompany::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function pickupStore(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'pickup_store_id');
    }

    public function isPickup(): bool
    {
        return $this->delivery_provider === 'pickup';
    }

    public function isPaid(): bool
    {
        return $this->payment_status === self::PAYMENT_PAID;
    }

    public function isOnlinePayment(): bool
    {
        return $this->payment_method === 'online_payment';
    }
}
