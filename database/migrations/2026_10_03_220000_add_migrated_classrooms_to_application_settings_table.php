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
        if (Schema::hasTable('application_settings')) {
            Schema::table('application_settings', function (Blueprint $table) {
                if (!Schema::hasColumn('application_settings', 'migrated_classrooms')) {
                    $table->longText('migrated_classrooms')->nullable()->after('last_migration_sent_at');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('application_settings')) {
            Schema::table('application_settings', function (Blueprint $table) {
                if (Schema::hasColumn('application_settings', 'migrated_classrooms')) {
                    $table->dropColumn('migrated_classrooms');
                }
            });
        }
    }
};
