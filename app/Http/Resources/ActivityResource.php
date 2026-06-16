<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
       return [
            'id'          => $this->id,
            'description' => $this->description,
            'created_at'  => $this->created_at->format('d-m-Y H:i'),
            'icon'        => $this->icon ?? null,
            'event'        => $this->event ?? null,
            'subject_type'=> $this->subject_type,
            'subject_id'  => $this->subject_id,
            'causer'      => $this->causer ? [
                'id' => $this->causer->id,
                'name' => $this->causer->first_name . ' ' . $this->causer->last_name,
                'avatar' => $this->causer->avatar,
            ] : null,
        ];
    }
}
