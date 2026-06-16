<?php

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Product */
class FrontEndProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'price' => $this->getFormattedPrice(),
            'main_image_url' => $this->getFirstMediaUrl('images')
                ?: asset('storage/products/default.png'),
            'translations' => $this->translations,
            'discount' => $this->discount,
            'discount_type' => $this->discount_type,
            'rate' => $this->rate && $this->rate != 0 ? $this->rate : null,
            'free_shipping' => (bool) ($this->pivot?->free_shipping ?? false),
        ];
    }
}
