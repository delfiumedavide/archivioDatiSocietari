<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('family_status_declarations')) {
            return;
        }

        Schema::create('family_status_declarations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->smallInteger('anno');
            $table->string('stato_civile', 50)->nullable();
            $table->string('generated_path')->nullable();
            $table->string('signed_path')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->text('note')->nullable();
            $table->foreignId('registered_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['member_id', 'anno']);
            $table->index('anno');
            $table->index('signed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('family_status_declarations');
    }
};
