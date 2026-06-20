<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ProductWrapper extends Pivot
{
    protected $table = 'product_wrapper';

    public $incrementing = true;

    protected $fillable = [
        'wrapper_id',
        'product_id',
        'display_order',
        'is_default',
        'free_shipping',
        'update_quantity',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'free_shipping' => 'boolean',
        'display_order' => 'integer',
        'update_quantity' => 'double',
    ];
}
