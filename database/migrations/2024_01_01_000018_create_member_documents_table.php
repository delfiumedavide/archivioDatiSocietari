<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['documento_identita', 'codice_fiscale']);
            $table->string('file_path', 500);
            $table->string('file_name_original', 255);
            $table->string('file_mime_type', 100);
            $table->unsignedBigInteger('file_size');
            $table->date('expiration_date')->nullable();
            $table->foreignId('uploaded_by')->constrained('users');
            $table->timestamps();

            $table->unique(['member_id', 'type']);
            $table->index('expiration_date');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_documents');
    }
};
