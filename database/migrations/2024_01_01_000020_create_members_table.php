<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('members')) {
            return;
        }
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100);
            $table->string('cognome', 100);
            $table->string('codice_fiscale', 16)->unique();
            $table->date('data_nascita')->nullable();
            $table->string('luogo_nascita_comune', 100)->nullable();
            $table->string('luogo_nascita_provincia', 2)->nullable();
            $table->string('nazionalita', 100)->default('Italiana');
            $table->enum('sesso', ['M', 'F'])->nullable();
            $table->string('stato_civile', 50)->nullable();
            $table->string('indirizzo_residenza', 255)->nullable();
            $table->string('citta_residenza', 100)->nullable();
            $table->string('provincia_residenza', 2)->nullable();
            $table->string('cap_residenza', 5)->nullable();
            $table->string('indirizzo_domicilio', 255)->nullable();
            $table->string('citta_domicilio', 100)->nullable();
            $table->string('provincia_domicilio', 2)->nullable();
            $table->string('cap_domicilio', 5)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('cellulare', 20)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('pec', 255)->nullable();
            $table->boolean('white_list')->default(false);
            $table->date('white_list_scadenza')->nullable();
            $table->text('note')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('codice_fiscale');
            $table->index('cognome');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
