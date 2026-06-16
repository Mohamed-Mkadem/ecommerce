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
        Schema::table('clients', function (Blueprint $table) {
            $table->string('phone2')->nullable()->after('phone');
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete()->after('state_id');
            $table->foreignId('locality_id')->nullable()->constrained()->nullOnDelete()->after('city_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('phone2');
            $table->dropColumn('locality_id');
            $table->dropColumn('city_id');
        });
    }
};
