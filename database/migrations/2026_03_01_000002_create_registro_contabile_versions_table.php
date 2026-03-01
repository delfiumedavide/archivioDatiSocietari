<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('registro_contabile_versions')) {
            return;
        }

        Schema::create('registro_contabile_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registro_id')
                  ->constrained('registri_contabili')
                  ->cascadeOnDelete();
            $table->unsignedInteger('version');
            $table->string('file_path', 500);
            $table->unsignedBigInteger('file_size');
            $table->string('file_mime_type', 100);
            $table->foreignId('uploaded_by')->constrained('users');
            $table->text('change_notes')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['registro_id', 'version']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registro_contabile_versions');
    }
};
