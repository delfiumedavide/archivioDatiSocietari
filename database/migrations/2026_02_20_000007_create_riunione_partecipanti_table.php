<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('riunione_partecipanti')) {
            return;
        }

        Schema::create('riunione_partecipanti', function (Blueprint $table) {
            $table->id();
            $table->foreignId('riunione_id')->constrained('riunioni')->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->enum('presenza', ['presente', 'assente', 'delegato'])->default('presente');
            $table->string('note', 255)->nullable();
            $table->timestamps();

            $table->unique(['riunione_id', 'member_id']);
            $table->index('riunione_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riunione_partecipanti');
    }
};
