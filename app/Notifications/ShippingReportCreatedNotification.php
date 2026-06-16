<?php

namespace App\Notifications;

use App\Models\ShippingReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ShippingReportCreatedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public ShippingReport $shippingReport)
    {
        $this->shippingReport = $shippingReport;
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
            'icon' => 'ri-file-text-fill',
            'message' => 'The shipping report has been created.',
            'url' => route('shipping_reports.index'),
            'name' => $this->shippingReport->name,
            'created_at' => now()->format('d - m - Y : H:i')
        ];
    }
    public function toBroadcast(object $notifiable): array
    {
        return  [
            'icon' => 'ri-file-text-fill',
            'message' => 'The shipping report has been created.',
            'url' => route('shipping_reports.index'),
            'name' => $this->shippingReport->name,
            'created_at' => now()->format('d - m - Y : H:i')
        ];
    }


    public function databaseType(object $notifiable): string
    {
        return 'shipping-report';
    }
}
