<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_relationships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('child_company_id')->constrained('companies')->cascadeOnDelete();
            $table->enum('relationship_type', ['controllante', 'controllata', 'collegata', 'partecipata']);
            $table->decimal('quota_percentuale', 5, 2)->nullable();
            $table->date('data_inizio')->nullable();
            $table->date('data_fine')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->unique(['parent_company_id', 'child_company_id', 'relationship_type'], 'company_rel_unique');
            $table->index('parent_company_id');
            $table->index('child_company_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_relationships');
    }
};
