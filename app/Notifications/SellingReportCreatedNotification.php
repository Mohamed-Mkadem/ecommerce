<?php

namespace App\Notifications;

use App\Models\SellingReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class SellingReportCreatedNotification extends Notification
{
    use Queueable;

    public function __construct(public SellingReport $sellingReport)
    {
        $this->sellingReport = $sellingReport;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase(object $notifiable): array
    {
        return  [
            'icon' => 'ri-file-chart-fill',
            'message' => 'The selling report has been created.',
            'url' => route('selling_reports.index'),
            'name' => $this->sellingReport->name,
            'created_at' => now()->format('d - m - Y : H:i')
        ];
    }

    public function toBroadcast(object $notifiable): array
    {
        return  [
            'icon' => 'ri-file-chart-fill',
            'message' => 'The selling report has been created.',
            'url' => route('selling_reports.index'),
            'name' => $this->sellingReport->name,
            'created_at' => now()->format('d - m - Y : H:i')
        ];
    }

    public function databaseType(object $notifiable): string
    {
        return 'selling-report';
    }
}
