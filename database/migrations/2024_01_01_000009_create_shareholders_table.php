<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shareholders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->enum('tipo', ['persona_fisica', 'persona_giuridica']);
            $table->string('nome', 255);
            $table->string('codice_fiscale', 16)->nullable();
            $table->decimal('quota_percentuale', 5, 2);
            $table->decimal('quota_valore', 15, 2)->nullable();
            $table->date('data_ingresso')->nullable();
            $table->date('data_uscita')->nullable();
            $table->decimal('diritti_voto', 5, 2)->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('company_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shareholders');
    }
};
