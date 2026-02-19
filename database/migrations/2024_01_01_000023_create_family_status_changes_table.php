<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('family_status_changes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->string('stato_civile', 50);
            $table->date('data_variazione');
            $table->text('note')->nullable();
            $table->foreignId('registered_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('member_id');
            $table->index('data_variazione');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('family_status_changes');
    }
};
