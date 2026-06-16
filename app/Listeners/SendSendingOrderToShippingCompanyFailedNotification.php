<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\SendingOrderToShippingCompanyFailed;
use App\Notifications\SendingOrderToShippingCompanyNotification;

class SendSendingOrderToShippingCompanyFailedNotification implements ShouldQueue
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
    public function handle(SendingOrderToShippingCompanyFailed $event): void
    {
        $notifiables = User::where('status', 'active')->get();

        foreach ($notifiables as $notifiable) {
            $notifiable->notify(new SendingOrderToShippingCompanyNotification($event->order));
        }
    }
}
