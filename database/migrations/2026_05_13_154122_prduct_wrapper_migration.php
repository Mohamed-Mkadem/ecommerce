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
        Schema::create('product_wrapper', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wrapper_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->integer('display_order')->default(0);
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            // Ensure a product can only be in a wrapper once
            $table->unique(['wrapper_id', 'product_id']);

            // indexes
            $table->index('display_order');
            $table->index('is_default');
            $table->index('wrapper_id');
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_wrapper');
    }
};
