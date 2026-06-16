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
        Schema::create('shipping_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('orders_count');
            $table->string('name')->unique();
            $table->string('excel_file_path');
            $table->string('pdf_file_path');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_reports');
    }
};
