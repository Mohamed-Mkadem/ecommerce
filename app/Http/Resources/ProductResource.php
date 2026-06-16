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
        return [
            'id' => $this->id,
            'description' => $this->description,
            'name' => $this->name,
            'price' => $this->getFormattedPrice(),
            'status' => $this->status,
            'type' => $this->type,
            'rate' => $this->rate && $this->rate != 0 ? $this->rate : null,
            'main_image_url' => $this->getFirstMediaUrl('images') ?: asset('storage/products/default.png'),
            'ends_at' => $this->getFormattedEndsAtDate(),
            'media' => $this->getMedia('images')->toArray(),
            'orders_count' => $this->orders()->where('status', 'delivered')->sum('order_product.quantity'),
            'translations' => $this->translations,
            'reviews_count' => $this->reviews()->count(),
            'shipping_name' => $this->shipping_name,
            'discount_type' => $this->discount_type,
            'discount' => $this->discount,

        ];
    }
}
