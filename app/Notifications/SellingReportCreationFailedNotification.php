<?php

namespace App\Notifications;

use App\Models\SellingReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class SellingReportCreationFailedNotification extends Notification
{
    use Queueable;

    public function __construct(public string $name)
    {
        //
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase(object $notifiable): array
    {
        return  [
            'icon' => 'ri-error-warning-fill',
            'message' => 'The creation of the selling report has been failed',
            'url' => route('selling_reports.index'),
            'name' => $this->name,
            'created_at' => now()->format('d - m - Y : H:i')
        ];
    }

    public function toBroadcast(object $notifiable): array
    {
        return  [
            'icon' => 'ri-error-warning-fill',
            'message' => 'The creation of the selling report has been failed',
            'url' => route('selling_reports.index'),
            'name' => $this->name,
            'created_at' => now()->format('d - m - Y : H:i')
        ];
    }

    public function databaseType(object $notifiable): string
    {
        return 'selling-report';
    }
}
