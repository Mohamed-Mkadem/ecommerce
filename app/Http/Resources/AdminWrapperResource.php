<?php

namespace App\Http\Resources;

use App\Models\Product;
use App\Models\Wrapper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Wrapper */
class AdminWrapperResource extends JsonResource
{
    public function __construct(
        $resource,
        protected bool $detailed = false,
    ) {
        parent::__construct($resource);
    }

    public function toArray(Request $request): array
    {
        $default = $this->defaultProduct();

        $data = [
            'id' => $this->id,
            'title' => $this->title,
            'caption' => $this->caption,
            'slug' => $this->slug,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'products_count' => $this->products_count
                ?? ($this->relationLoaded('products') ? $this->products->count() : 0),
            'main_image_url' => $default?->getFirstMediaUrl('images')
                ?: asset('storage/products/default.png'),
            'price' => $default?->getFormattedPrice(),
            'default_product' => $default ? $this->formatProduct($default) : null,
            'created_at' => $this->created_at?->format('d-m-Y H:i'),
        ];

        if ($this->detailed) {
            $data['variants'] = $this->products
                ->sortBy(fn(Product $product) => $product->pivot->display_order)
                ->values()
                ->map(fn(Product $product) => array_merge(
                    $this->formatProduct($product),
                    [
                        'display_order' => $product->pivot->display_order,
                        'is_default' => (bool) $product->pivot->is_default,
                        'free_shipping' => (bool) $product->pivot->free_shipping,
                    ]
                ));
        }

        return $data;
    }

    private function formatProduct(Product $product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'type' => $product->type,
            'status' => $product->status,
            'price' => $product->getFormattedPrice(),
            'main_image_url' => $product->getFirstMediaUrl('images')
                ?: asset('storage/products/default.png'),
        ];
    }
}
