<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'category' => $this->category,
            'amount' => number_format($this->amount / 1000, 3, '.', ''),
            'type' => $this->type,
            'created_at' => \Carbon\Carbon::parse($this->created_at)->format('d-m-Y : H:i'),
            'invoiceable_type' => $this->invoiceable_type,
            'invoiceable' => $this->invoiceable,

        ];
    }
}
