<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('app_settings')) {
            return;
        }

        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();

            // Branding
            $table->string('app_name', 100)->default('Archivio Societario');
            $table->string('app_subtitle', 100)->default('Gruppo di Martino');
            $table->string('login_title', 100)->nullable();
            $table->string('logo_path')->nullable();
            $table->string('favicon_path')->nullable();

            // Holding data
            $table->string('holding_ragione_sociale', 200)->nullable();
            $table->string('holding_forma_giuridica', 50)->nullable();
            $table->string('holding_codice_fiscale', 16)->nullable();
            $table->string('holding_partita_iva', 11)->nullable();
            $table->string('holding_indirizzo', 200)->nullable();
            $table->string('holding_citta', 100)->nullable();
            $table->string('holding_provincia', 2)->nullable();
            $table->string('holding_cap', 5)->nullable();
            $table->string('holding_telefono', 20)->nullable();
            $table->string('holding_email', 100)->nullable();
            $table->string('holding_pec', 100)->nullable();
            $table->string('holding_rea', 50)->nullable();
            $table->decimal('holding_capitale_sociale', 15, 2)->nullable();

            // Declaration PDF header
            $table->string('declaration_header_title', 200)->nullable();
            $table->string('declaration_header_subtitle', 200)->nullable();
            $table->string('declaration_footer_text', 500)->nullable();

            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Insert default row
        DB::table('app_settings')->insert([
            'id' => 1,
            'app_name' => 'Archivio Societario',
            'app_subtitle' => 'Gruppo di Martino',
            'declaration_header_title' => 'Gruppo Di Martino',
            'declaration_footer_text' => 'Generato dal sistema Archivio Societario',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};
