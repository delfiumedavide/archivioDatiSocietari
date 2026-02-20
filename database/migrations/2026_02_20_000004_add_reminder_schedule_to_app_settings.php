<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('app_settings', 'expiry_reminder_enabled')) {
            Schema::table('app_settings', function (Blueprint $table) {
                $table->boolean('expiry_reminder_enabled')->default(true)->after('expiry_reminder_days');
                $table->string('expiry_reminder_time', 5)->default('08:00')->after('expiry_reminder_enabled');
            });
        }
    }

    public function down(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            $table->dropColumn(['expiry_reminder_enabled', 'expiry_reminder_time']);
        });
    }
};
