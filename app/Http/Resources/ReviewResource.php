<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\ClientResource;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
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
            'product' => new ProductResource($this->product),
            'client_name' => $this->client_name,
            'stars' => $this->stars,
            'comment' => $this->comment,
            'date' => \Carbon\Carbon::parse($this->created_at)->format('d-m-Y'),
            'product' => new ProductResource($this->product),
            'client' => new ClientResource($this->client),
        ];
    }
}
