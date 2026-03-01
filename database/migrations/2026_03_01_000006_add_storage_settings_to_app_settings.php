<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            $table->string('storage_mode', 20)->default('local')->after('smtp_from_name');
            $table->string('storage_external_path', 500)->nullable()->after('storage_mode');
        });
    }

    public function down(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            $table->dropColumn(['storage_mode', 'storage_external_path']);
        });
    }
};
