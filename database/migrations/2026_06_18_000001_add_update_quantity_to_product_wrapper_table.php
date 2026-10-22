<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('product_wrapper', function (Blueprint $table) {
            $table->double('update_quantity')->default(1)->after('free_shipping');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_wrapper', function (Blueprint $table) {
            $table->dropColumn('update_quantity');
        });
    }
};
