<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

require dirname(__DIR__, 3) . '/vendor/autoload.php';

$app = require dirname(__DIR__, 3) . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$phone = getenv('E2E_ORDER_PHONE') ?: '';
$marker = getenv('E2E_ORDER_MARKER') ?: '';

if (! preg_match('/^2\d{7}$/', $phone) || ! str_starts_with($marker, 'E2E Storefront Order ')) {
    fwrite(STDERR, "Refusing cleanup without a valid E2E order identity.\n");
    exit(1);
}

DB::transaction(function () use ($phone, $marker) {
    $clientIds = DB::table('clients')
        ->where('phone', $phone)
        ->where('name', $marker)
        ->pluck('id');

    $orderIds = DB::table('orders')
        ->where('phone', $phone)
        ->where('client_name', $marker)
        ->pluck('id');

    if ($orderIds->isNotEmpty()) {
        DB::table('order_product')->whereIn('order_id', $orderIds)->delete();
        DB::table('nrps')->whereIn('order_id', $orderIds)->delete();
        DB::table('notes')
            ->where('notable_type', 'App\\Models\\Order')
            ->whereIn('notable_id', $orderIds)
            ->delete();

        if (Schema::hasTable('activity_log')) {
            DB::table('activity_log')
                ->where('subject_type', 'App\\Models\\Order')
                ->whereIn('subject_id', $orderIds)
                ->delete();
        }

        DB::table('orders')->whereIn('id', $orderIds)->delete();
    }

    if ($clientIds->isNotEmpty()) {
        DB::table('notes')
            ->where('notable_type', 'App\\Models\\Client')
            ->whereIn('notable_id', $clientIds)
            ->delete();

        if (Schema::hasTable('activity_log')) {
            DB::table('activity_log')
                ->where('subject_type', 'App\\Models\\Client')
                ->whereIn('subject_id', $clientIds)
                ->delete();
        }

        DB::table('clients')->whereIn('id', $clientIds)->delete();
    }
});
