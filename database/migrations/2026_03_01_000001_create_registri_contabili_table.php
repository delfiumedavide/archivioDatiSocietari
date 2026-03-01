<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('registri_contabili')) {
            return;
        }

        Schema::create('registri_contabili', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('anno');
            $table->string('tipo', 100);
            $table->string('titolo', 255);
            $table->text('note')->nullable();
            $table->string('file_path', 500);
            $table->string('file_name_original', 255);
            $table->string('file_mime_type', 100);
            $table->unsignedBigInteger('file_size');
            $table->unsignedInteger('current_version')->default(1);
            $table->foreignId('uploaded_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index('company_id');
            $table->index('anno');
            $table->index('tipo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registri_contabili');
    }
};
