<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    // public function toArray(Request $request): array
    // {
    //     return parent::toArray($request);
    // }

    public function toArray(Request $request): array
    {
        $wrapper = $this->relationLoaded('wrappers')
            ? $this->wrappers->first()
            : $this->wrappers()->first();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->getFormattedPrice(),
            'orders_count' => $this->orders()->where('status', 'delivered')->sum('order_product.quantity'),
            'translations' => $this->translations,
            'shipping_name' => $this->shipping_name,
            'discount_type' => $this->discount_type,
            'discount' => $this->discount,
            'main_image_url' => $wrapper?->getFirstMediaUrl('images') ?: asset('storage/products/product.webp'),
            'wrapper' => $wrapper ? [
                'id' => $wrapper->id,
                'title' => $wrapper->title,
                'url' => route('wrappers.show', $wrapper),
            ] : null,

        ];
    }
}
