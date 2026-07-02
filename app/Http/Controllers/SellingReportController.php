<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\SellingReport;
use App\Jobs\GenerateSellingReport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreSellingReportRequest;

class SellingReportController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Selling_reports/Index', [
            'reports' => SellingReport::with('user')->orderBy('created_at', 'desc')->paginate(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Selling_reports/Create');
    }

    public function store(StoreSellingReportRequest $request)
    {
        $validated = $request->validated();

        GenerateSellingReport::dispatch($validated, Auth::id());

        return redirect()->back()->with('success', 'The selling report is being generated.');
    }

    public function destroy(SellingReport $sellingReport)
    {
        try {
            DB::beginTransaction();

            if ($sellingReport->excel_file_path && Storage::disk('local')->exists($sellingReport->excel_file_path)) {
                Storage::disk('local')->delete($sellingReport->excel_file_path);
            }
            $sellingReport->delete();

            DB::commit();
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back();
        }
    }

    public function downloadExcel($id)
    {
        $report = SellingReport::findOrFail($id);
        $filePath = $report->excel_file_path;

        if (!$filePath || !Storage::disk('local')->exists($filePath)) {
            abort(404);
        }

        return Storage::disk('local')->download($filePath);
    }
}
