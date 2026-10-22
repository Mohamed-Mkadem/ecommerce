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
        $tableName = config('activitylog.table_name');
        if (DB::getDriverName() === 'mysql') {
            DB::statement("CREATE INDEX activity_log_causer_desc_created_index ON {$tableName} (causer_id, description(50), created_at)");
        } else {
            Schema::table($tableName, function (Blueprint $table) {
                $table->index(['causer_id', 'description', 'created_at'], 'activity_log_causer_desc_created_index');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableName = config('activitylog.table_name');
        if (DB::getDriverName() === 'mysql') {
            DB::statement("DROP INDEX activity_log_causer_desc_created_index ON {$tableName}");
        } else {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropIndex('activity_log_causer_desc_created_index');
            });
        }
    }
};
