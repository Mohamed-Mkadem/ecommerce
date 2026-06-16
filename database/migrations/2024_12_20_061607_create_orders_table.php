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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients');
            $table->foreignId('state_id')->constrained('states');
            $table->foreignId('coupon_code_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('shipper_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('status', ['pending', 'confirmed', 'canceled', 'delivered', 'returned', 'shipped'])->default('pending');
            $table->unsignedBigInteger('amount');
            $table->unsignedInteger('shipping_cost');
            $table->string('client_name');
            $table->text('address');
            $table->text('note')->nullable();
            $table->string('phone');
            $table->date('delivery_date');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
