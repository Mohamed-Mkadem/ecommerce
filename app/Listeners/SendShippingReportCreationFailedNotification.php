<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\ShippingReportCreationFailed;
use App\Notifications\ShippingReportCreationFailedNotification;

class SendShippingReportCreationFailedNotification
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
    public function handle(ShippingReportCreationFailed $event): void
    {
        $notifiables = User::all();

        foreach ($notifiables as $notifiable) {
            $notifiable->notify(new ShippingReportCreationFailedNotification($event->name));
        }
    }
}
