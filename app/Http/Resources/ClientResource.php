<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
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
            'deleted_at' => $this->deleted_at,
            'name' => $this->name,
            'phone' => $this->phone,
            'phone2' => $this->phone2,
            'address' => $this->address,
            'state' => $this->state,
            'state_id' => $this->state_id,
            // 'orders_count' => $this->orders->count(),
            // 'spent' => number_format($this->orders()->where('status', 'delivered')->sum('amount') / 1000, 3, '.', ''),
            // 'reviews_count' => $this->reviews->count(),
            'orders_count' => $this->total_orders_count ?? 0, // Fallback to 0 if not set for some reason
            // Use the pre-calculated total_delivered_spent
            'spent' => number_format(($this->total_delivered_spent ?? 0) / 1000, 3, '.', ''), // Fallback to 0
            'reviews_count' => $this->whenLoaded('reviews', fn() => $this->reviews->count()), // Use whenLoaded
            'notes' => $this->notes->map(function ($note) {
                return [
                    'id' => $note->id,
                    'created_at' => Carbon::parse($note->created_at)->format('d-m-Y - H:i'),
                    'content' => $note->content,
                    'user' => $note->user
                ];
            })
        ];
    }
}
