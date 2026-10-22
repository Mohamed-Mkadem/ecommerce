<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderReportRequest;
use App\Jobs\GenerateOrderReport;
use App\Models\OrderReport;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class OrderReportController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Order_Reports/Index', [
            'reports' => OrderReport::with('user')->orderByDesc('created_at')->paginate(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Order_Reports/Create');
    }

    public function store(StoreOrderReportRequest $request)
    {
        GenerateOrderReport::dispatch($request->validated(), Auth::id());

        return redirect()->back()->with('success', 'The order report is being generated.');
    }

    public function download(OrderReport $orderReport)
    {
        abort_unless(Storage::disk('local')->exists($orderReport->excel_file_path), 404);

        return Storage::disk('local')->download($orderReport->excel_file_path);
    }

    public function destroy(OrderReport $order_report)
    {
        try {
            DB::transaction(function () use ($order_report) {
                Storage::disk('local')->delete($order_report->excel_file_path);
                $order_report->delete();
            });

            return redirect()->back()->with('success', 'Order report deleted successfully.');
        } catch (\Throwable $exception) {
            report($exception);

            return redirect()->back()->with('error', 'Failed to delete the order report.');
        }
    }

    public function clean()
    {
        $exitCode = Artisan::call('order-reports:prune');

        return redirect()->back()->with(
            $exitCode === 0 ? 'success' : 'error',
            $exitCode === 0 ? 'Old order reports cleaned successfully.' : 'Failed to clean order reports.'
        );
    }
}
