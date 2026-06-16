<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ClearTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('DELETE FROM order_product');
        DB::statement('DELETE FROM product_translations');
        DB::statement('DELETE FROM orders');
        DB::statement('DELETE FROM invoices');
        DB::statement('DELETE FROM products');
        DB::statement('DELETE FROM shippers');
        DB::statement('DELETE FROM clients');
        DB::statement('DELETE FROM media');
        DB::statement('DELETE FROM notifications');
        DB::statement('DELETE FROM shipping_reports');
    }
}
