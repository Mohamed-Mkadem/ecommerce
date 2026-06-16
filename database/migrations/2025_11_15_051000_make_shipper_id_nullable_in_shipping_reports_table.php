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
        Schema::table('shipping_reports', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['shipper_id']);
            // Make the column nullable
            $table->unsignedBigInteger('shipper_id')->nullable()->change();
            // Re-add the foreign key constraint
            $table->foreign('shipper_id')->references('id')->on('shippers')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipping_reports', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['shipper_id']);
            // Make the column not nullable
            $table->unsignedBigInteger('shipper_id')->nullable(false)->change();
            // Re-add the foreign key constraint
            $table->foreign('shipper_id')->references('id')->on('shippers')->cascadeOnDelete();
        });
    }
};

