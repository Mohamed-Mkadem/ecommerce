<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Order;
use App\Models\Shipper;
use App\Models\ShippingReport;
use App\Jobs\CreateShippingReport;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Events\ShippingReportCreated;
use App\Exports\ShippingReportExport;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreShippingReportRequest;

class ShippingReportController extends Controller
{

    public function downloadPdf($id)
    {
        $report = ShippingReport::findOrFail($id);
        $filePath = $report->pdf_file_path;


        if (!Storage::disk('local')->exists($filePath)) {
            abort(404);
        }

        return Storage::disk('local')->download($filePath);
    }

    public function downloadExcel($id)
    {
        $report = ShippingReport::findOrFail($id);
        $filePath = $report->excel_file_path;

        if (!Storage::disk('local')->exists($filePath)) {
            abort(404);
        }

        return Storage::disk('local')->download($filePath);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Admin/Shipping_Reports/Index', [
            'reports' => ShippingReport::orderby('created_at', 'desc')->paginate(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $shippers = Shipper::all();
        return Inertia::render('Admin/Shipping_Reports/Create', ['shippers' => $shippers]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreShippingReportRequest $request)
    {
        $validated = $request->validated();
        $grandTunisShipper = $validated['shipper_id'] == 'gt' ? true : false;
        CreateShippingReport::dispatch($validated, $grandTunisShipper);

        return redirect()->back();
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ShippingReport $shippingReport)
    {
        try {
            DB::beginTransaction();

            Storage::disk('local')->delete($shippingReport->excel_file_path);
            $shippingReport->delete();

            DB::commit();
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back();
        }
    }
}
