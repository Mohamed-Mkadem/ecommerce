<?php

namespace App\Notifications;

use App\Models\OrderReport;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderReportCreatedNotification extends Notification
{
    use Queueable;

    public function __construct(public OrderReport $orderReport)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase(object $notifiable): array
    {
        return $this->notificationData();
    }

    public function toBroadcast(object $notifiable): array
    {
        return $this->notificationData();
    }

    public function databaseType(object $notifiable): string
    {
        return 'order-report';
    }

    private function notificationData(): array
    {
        $name = $this->orderReport->name;

        return [
            'icon' => 'ri-file-chart-fill',
            'message' => 'The order report has been created.',
            'url' => route('order_reports.index'),
            'name' => $name,
            'created_at' => now()->format('d - m - Y : H:i'),
        ];
    }
}
