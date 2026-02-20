<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('riunioni')) {
            return;
        }

        Schema::create('riunioni', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->enum('tipo', ['cda', 'collegio_sindacale', 'assemblea_ordinaria', 'assemblea_straordinaria']);
            $table->dateTime('data_ora');
            $table->string('luogo', 255)->nullable();
            $table->enum('status', ['programmata', 'convocata', 'svolta', 'annullata'])->default('programmata');
            $table->text('ordine_del_giorno')->nullable();
            $table->string('convocazione_path')->nullable();
            $table->string('verbale_path')->nullable();
            $table->text('note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('company_id');
            $table->index('tipo');
            $table->index('status');
            $table->index('data_ora');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riunioni');
    }
};
