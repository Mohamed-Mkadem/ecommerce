<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Wrapper extends Model implements TranslatableContract, HasMedia
{
    /** @use HasFactory<\Database\Factories\WrapperFactory> */
    use HasFactory, Translatable, InteractsWithMedia;

    protected $fillable = [
        'caption',
        'slug',
        'is_active',
    ];

    public $translatedAttributes = ['title', 'description'];


    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_wrapper')
            ->using(ProductWrapper::class)
            ->withPivot('display_order', 'is_default', 'free_shipping', 'update_quantity')
            ->orderByPivot('display_order');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images');
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
