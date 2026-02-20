<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('app_settings', 'notification_emails')) {
            Schema::table('app_settings', function (Blueprint $table) {
                $table->text('notification_emails')->nullable()->after('declaration_footer_text');
                $table->unsignedInteger('expiry_reminder_days')->default(30)->after('notification_emails');
            });
        }
    }

    public function down(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            $table->dropColumn(['notification_emails', 'expiry_reminder_days']);
        });
    }
};
