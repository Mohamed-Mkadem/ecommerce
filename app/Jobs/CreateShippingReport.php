<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Order;
use App\Models\ShippingReport;
use Spatie\LaravelPdf\Enums\Unit;
use Illuminate\Support\Collection;
use Spatie\LaravelPdf\Facades\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Events\ShippingReportCreated;
use App\Exports\ShippingReportExport;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\ShippingReportCreationFailed;
use Illuminate\Support\Facades\Log;
use Spatie\Browsershot\Browsershot;
use Throwable;

class CreateShippingReport implements ShouldQueue
{
    use Queueable;
    public $timeout = 600;

    public $failOnTimeout = true;


    /**
     * Create a new job instance.
     */
    public function __construct(public $requestData, public $grandTunisShipper = false)
    {
        $this->requestData = $requestData;
        $this->grandTunisShipper = $grandTunisShipper;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $ordersQuery = Order::query()
            ->where('delivery_date', $this->requestData['date'])
            ->where('status', 'confirmed')
            ->with(['state', 'shipper', 'couponCode', 'products', 'locality', 'city', 'client']);

        if ($this->grandTunisShipper) {

            $ordersQuery->whereHas('shipper', function ($query) {
                $query->whereNotIn('name', ['Navex', 'DSGO'])
                    ->whereNull('deleted_at');
            });
        } else {

            $ordersQuery->where('shipper_id', $this->requestData['shipper_id']);
        }

        $orders = $ordersQuery->get();
        $excelFileName = 'rapport_' . now()->format('Y_m_d_H_i_s') . '.xlsx';
        $excelFilePath = 'reports/' . $excelFileName;

        $excelFileName = 'rapport_' . now()->format('Y_m_d_H_i_s') . '.xlsx';
        $excelFilePath = 'reports/' . $excelFileName;
        $pdfFileName = 'rapport_' . now()->format('Y_m_d_H_i_s') . '.pdf';
        $pdfFilePath = 'reports/' . $pdfFileName;


        Pdf::view('invoice', ['orders' => $orders])
            ->withBrowsershot(function ($browsershot) {
                $browsershot->noSandbox();
            })
            ->margins(8, 8, 8, 8, Unit::Pixel)
            ->disk('local')
            ->paperSize(214, 135, Unit::Millimeter)
            ->save($pdfFilePath);

        Excel::store(new ShippingReportExport($orders, $this->grandTunisShipper), $excelFilePath);
        $ordersCount = $orders->count();
        $shippingReport = ShippingReport::create([
            'name' => $this->requestData['name'],
            'excel_file_path' =>  $excelFilePath,
            'orders_count' => $ordersCount,
            'pdf_file_path' => $pdfFilePath,
            'date' => $this->requestData['date'],
            'shipper_id' => $this->grandTunisShipper ? null : $this->requestData['shipper_id'],
        ]);



        event(new ShippingReportCreated($shippingReport));
    }
    public function failed(\Throwable $e)
    {
        event(new ShippingReportCreationFailed($this->requestData['name']));
        Log::info($e);
    }
}
