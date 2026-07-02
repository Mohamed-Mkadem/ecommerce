<?php

namespace App\Jobs;

use App\Exports\SellingReportExport;
use App\Models\SellingReport;
use App\Models\User;
use App\Notifications\SellingReportCreatedNotification;
use App\Notifications\SellingReportCreationFailedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class GenerateSellingReport implements ShouldQueue
{
    use Queueable;

    public $timeout = 600;

    public $failOnTimeout = true;

    /**
     * Create a new job instance.
     */
    public function __construct(public array $requestData, public int $userId)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $rows = DB::table('orders as o')
            ->join('order_product as op', 'o.id', '=', 'op.order_id')
            ->join('products as p', 'op.product_id', '=', 'p.id')
            ->leftJoin('product_translations as pt', function ($join) {
                $join->on('p.id', '=', 'pt.product_id')
                    ->where('pt.locale', 'fr');
            })
            ->whereDate('o.created_at', '>=', $this->requestData['start_date'])
            ->whereDate('o.created_at', '<=', $this->requestData['end_date'])
            ->selectRaw('COALESCE(pt.name, p.shipping_name) as product_name, SUM(op.quantity) as total_quantity, COUNT(DISTINCT o.id) as number_of_orders')
            ->groupByRaw('COALESCE(pt.name, p.shipping_name)')
            ->orderByDesc('total_quantity')
            ->get();

        $excelFileName = 'selling_report_' . now()->format('Y_m_d_H_i_s') . '.xlsx';
        $excelFilePath = 'reports/' . $excelFileName;

        Excel::store(new SellingReportExport($rows), $excelFilePath);

        $sellingReport = SellingReport::create([
            'name' => $this->requestData['name'],
            'start_date' => $this->requestData['start_date'],
            'end_date' => $this->requestData['end_date'],
            'user_id' => $this->userId,
            'excel_file_path' => $excelFilePath,
        ]);

        $user = User::find($this->userId);

        if ($user) {
            $user->notify(new SellingReportCreatedNotification($sellingReport));
        }
    }

    public function failed(Throwable $e): void
    {
        $user = User::find($this->userId);

        if ($user) {
            $user->notify(new SellingReportCreationFailedNotification($this->requestData['name'] ?? 'Unnamed report'));
        }

        Log::error('Selling report generation failed.', [
            'report_name' => $this->requestData['name'] ?? null,
            'error' => $e->getMessage(),
        ]);
    }
}
