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
            'orders_count' => $this->total_orders_count ?? 0,    
            'spent' => number_format(($this->total_delivered_spent ?? 0) / 1000, 3, '.', ''),
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
