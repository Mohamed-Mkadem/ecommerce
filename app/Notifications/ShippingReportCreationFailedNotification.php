<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\ShippingReport;

class ShippingReportCreationFailedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public string $name)
    {
        $this->name = $name;
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
        return  [
            'icon' => 'ri-error-warning-line',
            'message' => 'The creation of the shipping report has been failed',
            'url' => route('shipping_reports.index'),
            'name' => $this->name,
            'created_at' => now()->format('d - m - Y : H:i')
        ];
    }
    public function toBroadcast(object $notifiable): array
    {
        return  [
            'icon' => 'ri-error-warning-line',
            'message' => 'The creation of the shipping report has been failed',
            'url' => route('shipping_reports.index'),
            'name' => $this->name,
            'created_at' => now()->format('d - m - Y : H:i')
        ];
    }


    public function databaseType(object $notifiable): string
    {
        return 'shipping-report';
    }
}
