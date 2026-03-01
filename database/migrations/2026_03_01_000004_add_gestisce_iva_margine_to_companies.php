<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->boolean('gestisce_iva_margine')
                  ->default(false)
                  ->after('is_active')
                  ->comment('Se true, i registri IVA per il regime del margine sono richiesti mensilmente');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('gestisce_iva_margine');
        });
    }
};
