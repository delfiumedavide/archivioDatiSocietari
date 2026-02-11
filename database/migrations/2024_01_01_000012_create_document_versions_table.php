<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('version');
            $table->string('file_path', 500);
            $table->bigInteger('file_size')->unsigned();
            $table->string('file_mime_type', 100);
            $table->foreignId('uploaded_by')->constrained('users');
            $table->text('change_notes')->nullable();
            $table->timestamp('created_at');

            $table->unique(['document_id', 'version']);
            $table->index('document_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_versions');
    }
};
