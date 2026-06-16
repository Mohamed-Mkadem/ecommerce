<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wrapper extends Model
{
    /** @use HasFactory<\Database\Factories\WrapperFactory> */
    use HasFactory;

    protected $fillable = [
        'caption',
        'title',
        'slug',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_wrapper')
            ->using(ProductWrapper::class)
            ->withPivot('display_order', 'is_default', 'free_shipping')
            ->orderByPivot('display_order');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function defaultProduct(): ?Product
    {
        $products = $this->relationLoaded('products')
            ? $this->products->values()
            : $this->products()->get();

        $default = $products->first(
            fn(Product $product) => (bool) $product->pivot->is_default
        );

        if ($default) {
            return $default;
        }

        return $products->sortBy(fn(Product $product) => $product->pivot->display_order)->first();
    }
}
