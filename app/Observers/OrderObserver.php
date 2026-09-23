<?php

namespace App\Observers;

use App\Models\Order;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     * Covers the case where an admin creates an order with a terminal status
     * (delivered or returned) directly, without going through the update flow.
     */
    public function created(Order $order): void
    {
        if (in_array($order->status, ['delivered', 'returned'])) {
            $client = $order->client()->withTrashed()->first();

            if ($client) {
                $client->recalculateDeliveryRate();
            }
        }
    }

    /**
     * Handle the Order "updated" event.
     * Recalculates the client's delivery rate whenever an order reaches
     * a terminal delivery status (delivered or returned).
     */
    public function updated(Order $order): void
    {
        if (! $order->wasChanged('status')) {
            return;
        }

        $newStatus = $order->status;

        if (in_array($newStatus, ['delivered', 'returned'])) {
            $client = $order->client()->withTrashed()->first();

            if ($client) {
                $client->recalculateDeliveryRate();
            }
        }
    }
}
