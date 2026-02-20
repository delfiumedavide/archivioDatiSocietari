<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('delibere')) {
            return;
        }

        Schema::create('delibere', function (Blueprint $table) {
            $table->id();
            $table->foreignId('riunione_id')->constrained('riunioni')->cascadeOnDelete();
            $table->unsignedInteger('numero');
            $table->string('oggetto', 500);
            $table->enum('esito', ['approvata', 'respinta', 'sospesa'])->default('approvata');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->unique(['riunione_id', 'numero']);
            $table->index('riunione_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delibere');
    }
};
