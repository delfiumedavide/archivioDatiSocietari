<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            // Rename columns that have different names
            if (Schema::hasColumn('members', 'luogo_nascita') && !Schema::hasColumn('members', 'luogo_nascita_comune')) {
                $table->renameColumn('luogo_nascita', 'luogo_nascita_comune');
            }
            if (Schema::hasColumn('members', 'comune_residenza') && !Schema::hasColumn('members', 'citta_residenza')) {
                $table->renameColumn('comune_residenza', 'citta_residenza');
            }
        });

        Schema::table('members', function (Blueprint $table) {
            // Add missing columns
            if (!Schema::hasColumn('members', 'luogo_nascita_provincia')) {
                $table->string('luogo_nascita_provincia', 2)->nullable()->after('luogo_nascita_comune');
            }
            if (!Schema::hasColumn('members', 'nazionalita')) {
                $table->string('nazionalita', 100)->default('Italiana')->after('luogo_nascita_provincia');
            }
            if (!Schema::hasColumn('members', 'sesso')) {
                $table->enum('sesso', ['M', 'F'])->nullable()->after('nazionalita');
            }
            if (!Schema::hasColumn('members', 'stato_civile')) {
                $table->string('stato_civile', 50)->nullable()->after('sesso');
            }
            if (!Schema::hasColumn('members', 'indirizzo_domicilio')) {
                $table->string('indirizzo_domicilio', 255)->nullable()->after('cap_residenza');
            }
            if (!Schema::hasColumn('members', 'citta_domicilio')) {
                $table->string('citta_domicilio', 100)->nullable()->after('indirizzo_domicilio');
            }
            if (!Schema::hasColumn('members', 'provincia_domicilio')) {
                $table->string('provincia_domicilio', 2)->nullable()->after('citta_domicilio');
            }
            if (!Schema::hasColumn('members', 'cap_domicilio')) {
                $table->string('cap_domicilio', 5)->nullable()->after('provincia_domicilio');
            }
            if (!Schema::hasColumn('members', 'cellulare')) {
                $table->string('cellulare', 20)->nullable()->after('telefono');
            }
            if (!Schema::hasColumn('members', 'pec')) {
                $table->string('pec', 255)->nullable()->after('email');
            }
            if (!Schema::hasColumn('members', 'white_list')) {
                $table->boolean('white_list')->default(false)->after('pec');
            }
            if (!Schema::hasColumn('members', 'white_list_scadenza')) {
                $table->date('white_list_scadenza')->nullable()->after('white_list');
            }
            if (!Schema::hasColumn('members', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('note');
            }
            if (!Schema::hasColumn('members', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        // Not reversible - these columns are now part of the schema
    }
};
