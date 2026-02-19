<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            // Make company_id nullable (document can belong to a member instead)
            $table->foreignId('company_id')->nullable()->change();

            // Add member_id FK
            $table->foreignId('member_id')->nullable()->after('company_id')->constrained()->cascadeOnDelete();
            $table->index('member_id');
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['member_id']);
            $table->dropIndex(['member_id']);
            $table->dropColumn('member_id');

            $table->foreignId('company_id')->nullable(false)->change();
        });
    }
};
