<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('family_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->string('nome', 100);
            $table->string('cognome', 100);
            $table->string('codice_fiscale', 16)->nullable();
            $table->string('relazione', 50);
            $table->date('data_nascita')->nullable();
            $table->string('luogo_nascita', 100)->nullable();
            $table->date('data_inizio')->nullable();
            $table->date('data_fine')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('member_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('family_members');
    }
};
