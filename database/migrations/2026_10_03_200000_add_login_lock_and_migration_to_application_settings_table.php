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
        Schema::table('application_settings', function (Blueprint $table) {
            $table->boolean('is_login_locked')->default(false)->after('target_year');
            $table->text('allowed_roles_when_locked')->nullable()->after('is_login_locked');
            $table->text('login_locked_message')->nullable()->after('allowed_roles_when_locked');
            $table->string('new_app_url')->nullable()->default('https://sim.cahayatasbih.or.id')->after('login_locked_message');
            $table->string('migration_token')->nullable()->after('new_app_url');
            $table->timestamp('last_migration_sent_at')->nullable()->after('migration_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('application_settings', function (Blueprint $table) {
            $table->dropColumn([
                'is_login_locked',
                'allowed_roles_when_locked',
                'login_locked_message',
                'new_app_url',
                'migration_token',
                'last_migration_sent_at',
            ]);
        });
    }
};
