<?php

namespace App\Listeners;

use App\Models\User;
use App\Events\ReviewCreated;
use App\Models\Review;
use App\Notifications\ReviewCreatedNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendReviewCreatedNotification
{
    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     */
    public function handle(ReviewCreated $event): void
    {
        $notifiables = User::all();

        foreach ($notifiables as $notifiable) {
            $notifiable->notify(new ReviewCreatedNotification($event->review));
        }
    }
}
