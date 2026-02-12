<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100);
            $table->string('cognome', 100);
            $table->string('codice_fiscale', 16)->unique();
            $table->date('data_nascita')->nullable();
            $table->string('luogo_nascita', 150)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('telefono', 50)->nullable();
            $table->string('indirizzo_residenza', 255)->nullable();
            $table->string('comune_residenza', 150)->nullable();
            $table->string('provincia_residenza', 2)->nullable();
            $table->string('cap_residenza', 10)->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['cognome', 'nome']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
