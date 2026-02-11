<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('denominazione', 255);
            $table->string('codice_fiscale', 16)->nullable()->unique();
            $table->string('partita_iva', 11)->nullable()->unique();
            $table->string('pec', 255)->nullable();
            $table->string('forma_giuridica', 100)->nullable();
            $table->string('sede_legale_indirizzo', 255)->nullable();
            $table->string('sede_legale_citta', 100)->nullable();
            $table->string('sede_legale_provincia', 5)->nullable();
            $table->string('sede_legale_cap', 5)->nullable();
            $table->decimal('capitale_sociale', 15, 2)->nullable();
            $table->decimal('capitale_versato', 15, 2)->nullable();
            $table->date('data_costituzione')->nullable();
            $table->string('numero_rea', 20)->nullable();
            $table->string('cciaa', 100)->nullable();
            $table->string('codice_ateco', 10)->nullable();
            $table->text('descrizione_attivita')->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('sito_web', 255)->nullable();
            $table->text('note')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('logo_path', 500)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('denominazione');
            $table->index('codice_fiscale');
            $table->index('partita_iva');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
