<?php

namespace App\Jobs;

use App\Exports\OrderReportExport;
use App\Models\Order;
use App\Models\OrderReport;
use App\Models\User;
use App\Notifications\OrderReportCreatedNotification;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class GenerateOrderReport implements ShouldQueue
{
    use Queueable;

    public int $timeout = 600;

    public bool $failOnTimeout = true;

    public function __construct(public array $requestData, public int $userId)
    {
    }

    public function handle(): void
    {
        $startDate = Carbon::parse($this->requestData['start_date'])->startOfDay();
        $endDate = Carbon::parse($this->requestData['end_date'])->endOfDay();

        $countsByDate = Order::query()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as report_date')
            ->selectRaw('COUNT(*) as total_order')
            ->selectRaw("SUM(CASE WHEN source = 'admin' THEN 1 ELSE 0 END) as admin_orders")
            ->selectRaw("SUM(CASE WHEN source = 'client' THEN 1 ELSE 0 END) as storefront_orders")
            ->groupBy('report_date')
            ->get()
            ->keyBy('report_date');

        $rows = collect(CarbonPeriod::create($startDate->copy()->startOfDay(), $endDate->copy()->startOfDay()))
            ->map(function (Carbon $date) use ($countsByDate) {
                $counts = $countsByDate->get($date->toDateString());

                return [
                    'date' => $date->toDateString(),
                    'total_order' => (int) ($counts->total_order ?? 0),
                    'admin_orders' => (int) ($counts->admin_orders ?? 0),
                    'storefront_orders' => (int) ($counts->storefront_orders ?? 0),
                ];
            });

        $filePath = 'reports/order_report_' . now()->format('Y_m_d_His') . '_' . uniqid() . '.xlsx';
        Excel::store(new OrderReportExport($rows), $filePath, 'local');

        $report = OrderReport::create([
            'name' => $this->requestData['name'],
            'start_date' => $this->requestData['start_date'],
            'end_date' => $this->requestData['end_date'],
            'excel_file_path' => $filePath,
            'user_id' => $this->userId,
        ]);

        User::query()->where('role', 'admin')->each(function (User $admin) use ($report) {
            $admin->notify(new OrderReportCreatedNotification($report));
        });
    }

    public function failed(Throwable $exception): void
    {
        Log::error('Order report generation failed.', [
            'report_name' => $this->requestData['name'] ?? null,
            'error' => $exception->getMessage(),
        ]);
    }
}
