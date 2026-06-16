<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

class RoleChangedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct() {}

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

        $message = $notifiable->role == 'admin'         ? 'An admin changed your role to admin' : 'An admin changed your role to moderator';
        return [
            'title' => 'Role changed!',
            'message' => $message,
            'icon' => 'ri-id-card-line',
            'url' => route('profile.edit'),
            'created_at' => now()->format('d - m - Y : H:i')
        ];
    }
    public function toBroadcast(object $notifiable): BroadcastMessage
    {

        $message = $notifiable->role == 'admin' ? 'An admin changed your role to be an admin' : 'An admin changed your role to be a moderator';
        return new BroadcastMessage([
            'title' => 'Role changed!',
            'message' => $message,
            'icon' => 'ri-id-card-line',
            'url' => route('profile.edit'),
            'created_at' => now()->format('d - m - Y : H:i')
        ]);
    }
    public function databaseType(object $notifiable): string
    {
        return 'role-changed';
    }
}
