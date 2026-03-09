<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommercemlExchangeLog extends Model
{
    protected $fillable = [
        'type',
        'mode',
        'session_id',
        'filename',
        'status',
        'message',
        'products_created',
        'products_updated',
        'categories_created',
        'categories_updated',
        'orders_exported',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'products_created' => 'integer',
            'products_updated' => 'integer',
            'categories_created' => 'integer',
            'categories_updated' => 'integer',
            'orders_exported' => 'integer',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }
}
