<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registri_contabili', function (Blueprint $table) {
            $table->unsignedTinyInteger('mese')
                  ->nullable()
                  ->after('anno')
                  ->comment('1-12, valorizzato solo per registri IVA mensili');
        });
    }

    public function down(): void
    {
        Schema::table('registri_contabili', function (Blueprint $table) {
            $table->dropColumn('mese');
        });
    }
};
