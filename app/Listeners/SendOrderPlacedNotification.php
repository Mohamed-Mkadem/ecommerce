<?php

namespace App\Listeners;

use App\Models\User;
use App\Events\OrderPlaced;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\OrderPlacedNotification;

class SendOrderPlacedNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderPlaced $event): void
    {
        $notifiables = User::all();

        foreach ($notifiables as $notifiable) {
            $notifiable->notify(new OrderPlacedNotification($event->order));
        }
    }
}
