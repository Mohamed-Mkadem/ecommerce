<?php

namespace App\Notifications;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

class ReviewCreatedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Review $review)
    {
        $this->review = $review;
    }


    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'New Review!',
            'stars' => $this->review->stars,
            'message' => 'A new review has been placed!',
            'icon' => 'ri-star-line',
            'url' => route('reviews.index'),
            'created_at' => now()->format('d - m - Y : H:i')
        ];
    }
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => 'New Review!',
            'stars' => $this->review->stars,
            'message' => 'A new review has been placed!',
            'icon' => 'ri-star-line',
            'url' => route('reviews.index'),
            'created_at' => now()->format('d - m - Y : H:i')
        ]);
    }
    public function databaseType(object $notifiable): string
    {
        return 'new-review';
    }
}
