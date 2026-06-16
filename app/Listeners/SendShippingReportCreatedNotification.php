<?php

namespace App\Listeners;

use App\Models\User;
use App\Events\ShippingReportCreated;
use App\Notifications\ShippingReportCreatedNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendShippingReportCreatedNotification
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
    public function handle(ShippingReportCreated $event): void
    {
        $notifiables = User::all();

        foreach ($notifiables as $notifiable) {
            $notifiable->notify(new ShippingReportCreatedNotification($event->shippingReport));
        }
    }
}
