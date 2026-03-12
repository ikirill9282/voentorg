<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'path',
        'alt',
        'sort_order',
        'type',
        'video_url',
        'video_thumbnail',
        'orientation',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    protected function path(): Attribute
    {
        return Attribute::make(
            get: function (string $value) {
                if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
                    return $value;
                }

                return asset('storage/' . $value);
            },
        );
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function isVideo(): bool
    {
        return $this->type === 'video';
    }

    public function isVerticalVideo(): bool
    {
        return $this->isVideo() && $this->orientation === 'vertical';
    }

    public function isHorizontalVideo(): bool
    {
        return $this->isVideo() && $this->orientation === 'horizontal';
    }
}
