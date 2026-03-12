<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CatalogBanner extends Model
{
    protected $fillable = [
        'title',
        'category_id',
        'images',
        'position',
        'display_mode',
        'link_url',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'images' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForCategory($query, ?int $categoryId)
    {
        return $query->where(function ($q) use ($categoryId) {
            $q->whereNull('category_id');
            if ($categoryId) {
                $q->orWhere('category_id', $categoryId);
            }
        });
    }
}
