<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('document_category_id')->constrained('document_categories');
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->string('file_path', 500);
            $table->string('file_name_original', 255);
            $table->string('file_mime_type', 100);
            $table->bigInteger('file_size')->unsigned();
            $table->unsignedInteger('current_version')->default(1);
            $table->date('expiration_date')->nullable();
            $table->boolean('expiration_notified')->default(false);
            $table->enum('expiration_status', ['valid', 'expiring', 'expired'])->default('valid');
            $table->foreignId('uploaded_by')->constrained('users');
            $table->boolean('is_archived')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index('company_id');
            $table->index('document_category_id');
            $table->index('expiration_date');
            $table->index('expiration_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
