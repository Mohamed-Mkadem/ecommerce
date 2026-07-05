<?php

namespace Tests\Feature;

use App\Models\ShippingReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ShippingReportPruneCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_deletes_shipping_reports_older_than_seven_days_and_their_files(): void
    {
        Storage::fake('local');

        $oldExcelPath = 'reports/old.xlsx';
        $oldPdfPath = 'reports/old.pdf';
        Storage::disk('local')->put($oldExcelPath, 'old excel');
        Storage::disk('local')->put($oldPdfPath, 'old pdf');

        $oldReportId = DB::table('shipping_reports')->insertGetId([
            'name' => 'Old report',
            'orders_count' => 1,
            'excel_file_path' => $oldExcelPath,
            'pdf_file_path' => $oldPdfPath,
            'date' => now()->subDays(8)->toDateString(),
            'shipper_id' => null,
            'created_at' => now()->subDays(8),
            'updated_at' => now()->subDays(8),
        ]);

        $newExcelPath = 'reports/new.xlsx';
        $newPdfPath = 'reports/new.pdf';
        Storage::disk('local')->put($newExcelPath, 'new excel');
        Storage::disk('local')->put($newPdfPath, 'new pdf');

        $newReportId = DB::table('shipping_reports')->insertGetId([
            'name' => 'Recent report',
            'orders_count' => 1,
            'excel_file_path' => $newExcelPath,
            'pdf_file_path' => $newPdfPath,
            'date' => now()->subDays(2)->toDateString(),
            'shipper_id' => null,
            'created_at' => now()->subDays(2),
            'updated_at' => now()->subDays(2),
        ]);

        $this->assertDatabaseHas('shipping_reports', ['id' => $oldReportId]);
        $this->assertDatabaseHas('shipping_reports', ['id' => $newReportId]);

        Artisan::call('shipping-reports:prune');

        $this->assertDatabaseMissing('shipping_reports', ['id' => $oldReportId]);
        $this->assertDatabaseHas('shipping_reports', ['id' => $newReportId]);
        $this->assertFalse(Storage::disk('local')->exists($oldExcelPath));
        $this->assertFalse(Storage::disk('local')->exists($oldPdfPath));
        $this->assertTrue(Storage::disk('local')->exists($newExcelPath));
        $this->assertTrue(Storage::disk('local')->exists($newPdfPath));
    }
}
