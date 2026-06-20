<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wrapper_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wrapper_id')->constrained()->cascadeOnDelete();
            $table->string('locale')->index();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unique(['wrapper_id', 'locale']);
        });

        $defaultLocale = config('app.locale') ?: 'en';

        DB::table('wrappers')->orderBy('id')->lazy()->each(function ($wrapper) use ($defaultLocale) {
            DB::table('wrapper_translations')->insert([
                'wrapper_id' => $wrapper->id,
                'locale' => $defaultLocale,
                'title' => $wrapper->title,
                'description' => $wrapper->description,
            ]);
        });

        Schema::table('wrappers', function (Blueprint $table) {
            $table->dropColumn(['title', 'description']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wrappers', function (Blueprint $table) {
            $table->string('title');
            $table->text('description')->nullable();
        });

        $defaultLocale = config('app.locale') ?: 'en';

        DB::table('wrapper_translations')
            ->where('locale', $defaultLocale)
            ->orderBy('wrapper_id')
            ->lazy()
            ->each(function ($translation) {
                DB::table('wrappers')
                    ->where('id', $translation->wrapper_id)
                    ->update([
                        'title' => $translation->title,
                        'description' => $translation->description,
                    ]);
            });

        Schema::dropIfExists('wrapper_translations');
    }
};
