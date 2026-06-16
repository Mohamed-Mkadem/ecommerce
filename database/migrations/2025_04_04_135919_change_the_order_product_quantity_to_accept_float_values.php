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
                Schema::table('order_product', function (Blueprint $table) {
                    $table->float('quantity', 3, 1)->unsigned()->change();
                });
            }
    


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_product', function (Blueprint $table) {
            $table->unsignedInteger('quantity');
        });
    }
};
