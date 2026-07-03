<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'translations' => $this->translations,
            'shipping_cost' => $this->shipping_cost / 1000,
            'delivery_cost' => $this->delivery_cost / 1000,
            'return_cost' => $this->return_cost / 1000,
            'default_shipper' => $this->defaultShipper
        ];
    }
}
