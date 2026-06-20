<?php

namespace App\Http\Resources;

use App\Models\Wrapper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Wrapper */
class WrapperListingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $default = $this->defaultProduct();

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'caption' => $this->caption,
            'name' => $this->title,
            'description' => $this->description,
            'main_image_url' => $this->getFirstMediaUrl('images')
                ?: asset('storage/products/default.png'),
            'price' => $default?->getFormattedPrice(),
            'rate' => $default && $default->rate != 0 ? $default->rate : null,
            'discount' => $default?->discount,
            'discount_type' => $default?->discount_type,
            'default_product' => $default
                ? (new FrontEndProductResource($default))->resolve()
                : null,
        ];
    }
}
