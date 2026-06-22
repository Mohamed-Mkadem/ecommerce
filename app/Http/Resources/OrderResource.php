<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $client = $this->client;
        $recentClientOrders = $client
            ? Order::withTrashed()
            ->where('client_id', $client->id)
            ->latest('created_at')
            ->take(5)
            ->get()
            ->map(fn($order) => [
                'id' => $order->id,
                'status' => $order->status,
                'created_at' => Carbon::parse($order->created_at)->format('d-m-Y - H:i'),
                'delivery_date' => $order->delivery_date
                    ? Carbon::parse($order->delivery_date)->format('d-m-Y')
                    : null,
                'url' => route('orders.show', $order),
            ])
            : [];

        return [
            'id' => $this->id,
            'deleted_at' => $this->deleted_at,
            'client_name' => $this->client_name,
            'state_id' => $this->state_id,
            'shipping_cost' => $this->shipping_cost / 1000,
            'status' => $this->status,
            'amount' => number_format($this->amount / 1000, 3, '.', ''),
            'address' => $this->address,
            'phone' => $this->phone,
            'phone2' => $this->phone2,
            'note' => $this->note,
            'delivery_date' => Carbon::parse($this->delivery_date)->format('d-m-Y'),
            'created_at' => Carbon::parse($this->created_at)->format('d-m-Y - H:i'),
            'coupon' =>  $this->couponCode,
            'shipper' => $this->shipper ?? null,
            'client' => [
                'id' => $client?->id,
                'name' => $client?->name,
                'phone' => $client?->phone,
                'phone2' => $client?->phone2,
                'address' => $client?->address,
                'deleted_at' => $client?->deleted_at,
                'orders_count' => $client ? Order::withTrashed()->where('client_id', $client->id)->count() : 0,
                'recent_orders' => $recentClientOrders,
            ],
            'state' => $this->state,
            'city' => $this->city,
            'locality' => $this->locality,
            'products' => $this->products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'deleted_at' => $product->deleted_at,
                    'pivot' => [
                        'price' => number_format($product->pivot->price / 1000, 3, '.', ''),
                        'sub_total' => number_format($product->pivot->sub_total / 1000, 3, '.', ''),
                        'quantity' => $product->pivot->quantity,
                    ],
                    'main_image_url' => asset('storage/products/product.webp'),
                ];
            }),

            'notes' => $this->notes->map(function ($note) {
                return [
                    'id' => $note->id,
                    'created_at' => Carbon::parse($note->created_at)->format('d-m-Y - H:i'),
                    'content' => $note->content,
                    'user' => $note->user
                ];
            }),
            'nrp' => $this->nrp,
            'activities' => $this->activities->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'created_at' => Carbon::parse($activity->created_at)->format('d-m-Y - H:i'),
                    'description' => $activity->description,
                    'event' => $activity->event,
                    'icon' => $activity->icon,
                    'causer_type' => $activity->causer_type,
                    'causer' => $activity->causer ? [
                        'name' => $activity->causer->first_name . ' ' . $activity->causer->last_name,
                        'avatar' => $activity->causer->avatar ?: asset('storage/avatars/default.png'),
                        'id' => $activity->causer->id
                    ] : null
                ];
            }),
            'free_shipping' => (bool) $this->free_shipping
        ];
    }
}
