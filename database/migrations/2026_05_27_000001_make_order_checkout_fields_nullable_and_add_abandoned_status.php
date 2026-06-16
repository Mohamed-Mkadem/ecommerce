<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE orders MODIFY status ENUM('pending', 'confirmed', 'canceled', 'delivered', 'returned', 'shipped', 'abandoned') DEFAULT 'pending'");

        Schema::table('orders', function (Blueprint $table) {
            $table->string('client_name')->nullable()->change();
            $table->text('address')->nullable()->change();
            $table->unsignedBigInteger('amount')->nullable()->change();
            $table->unsignedInteger('shipping_cost')->nullable()->change();
            $table->date('delivery_date')->nullable()->change();
            $table->foreignId('state_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE orders MODIFY status ENUM('pending', 'confirmed', 'canceled', 'delivered', 'returned', 'shipped') DEFAULT 'pending'");

        Schema::table('orders', function (Blueprint $table) {
            $table->string('client_name')->nullable(false)->change();
            $table->text('address')->nullable(false)->change();
            $table->unsignedBigInteger('amount')->nullable(false)->change();
            $table->unsignedInteger('shipping_cost')->nullable(false)->change();
            $table->date('delivery_date')->nullable(false)->change();
            $table->foreignId('state_id')->nullable(false)->change();
        });
    }
};
