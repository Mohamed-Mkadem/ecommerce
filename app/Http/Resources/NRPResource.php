<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NRPResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return
        [
            'id' => $this->id,
            'tries' => $this->tries,
            'order_id' => $this->order_id,
            'amount' => number_format($this->order->amount / 1000, 3, '.', ''),
            'phone' => $this->order->phone,
           'created_at' => Carbon::parse($this->created_at)->format('d-m-Y - H:i'),
            'updated_at' => Carbon::parse($this->updated_at)->format('d-m-Y - H:i'),
        ]
        ;
    }
}
