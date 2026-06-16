<?php

namespace App\Models;


use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Relations\Pivot;

class OrderProduct extends Pivot
{
    protected $fillable = [
        'order_id',
        'product_id',
        'price',
        'quantity',
        'sub_total',
    ];
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
