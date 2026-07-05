<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Storage;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('notifications:prune', function () {
    $cutoff = now()->subDays(7)->startOfDay();

    $deleted = DB::table('notifications')
        ->where('created_at', '<', $cutoff)
        ->delete();

    $this->info("Deleted {$deleted} notification(s).");
})->purpose('Delete notifications older than seven days');

Artisan::command('shipping-reports:prune', function () {
    $cutoff = now()->subDays(7)->startOfDay();

    $reports = DB::table('shipping_reports')
        ->where('created_at', '<', $cutoff)
        ->get();

    foreach ($reports as $report) {
        if (! empty($report->excel_file_path)) {
            Storage::disk('local')->delete($report->excel_file_path);
        }

        if (! empty($report->pdf_file_path)) {
            Storage::disk('local')->delete($report->pdf_file_path);
        }
    }

    $deleted = DB::table('shipping_reports')
        ->where('created_at', '<', $cutoff)
        ->delete();

    $this->info("Deleted {$deleted} shipping report(s).");
})->purpose('Delete shipping reports older than seven days and their files');

Schedule::command('notifications:prune')->weekly();
Schedule::command('shipping-reports:prune')->weekly();
