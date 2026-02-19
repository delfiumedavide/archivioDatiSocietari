<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('company_officers', 'member_id')) {
            Schema::table('company_officers', function (Blueprint $table) {
                $table->foreignId('member_id')->after('company_id')->constrained()->cascadeOnDelete();
            });
        }

        if (Schema::hasColumn('company_officers', 'nome')) {
            Schema::table('company_officers', function (Blueprint $table) {
                $table->dropColumn(['nome', 'cognome', 'codice_fiscale']);
            });
        }

        // Add unique index - use try/catch since Laravel 11 removed Doctrine introspection
        try {
            Schema::table('company_officers', function (Blueprint $table) {
                $table->unique(['member_id', 'company_id', 'ruolo'], 'officer_member_company_role_unique');
            });
        } catch (\Illuminate\Database\QueryException $e) {
            // Index already exists, skip
        }
    }

    public function down(): void
    {
        Schema::table('company_officers', function (Blueprint $table) {
            $table->dropUnique('officer_member_company_role_unique');
            $table->dropForeign(['member_id']);
            $table->dropColumn('member_id');
            $table->string('nome', 100)->after('company_id');
            $table->string('cognome', 100)->after('nome');
            $table->string('codice_fiscale', 16)->nullable()->after('cognome');
        });
    }
};
