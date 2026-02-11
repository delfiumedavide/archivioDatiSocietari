<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_officers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('nome', 100);
            $table->string('cognome', 100);
            $table->string('codice_fiscale', 16)->nullable();
            $table->string('ruolo', 100);
            $table->date('data_nomina');
            $table->date('data_scadenza')->nullable();
            $table->date('data_cessazione')->nullable();
            $table->decimal('compenso', 12, 2)->nullable();
            $table->text('poteri')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('company_id');
            $table->index('data_scadenza');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_officers');
    }
};
