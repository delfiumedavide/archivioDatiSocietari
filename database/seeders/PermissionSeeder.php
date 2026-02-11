<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Seed the permissions table.
     */
    public function run(): void
    {
        $permissions = [
            // Companies
            ['name' => 'companies.view', 'section' => 'companies', 'label' => 'Visualizza Società'],
            ['name' => 'companies.create', 'section' => 'companies', 'label' => 'Crea Società'],
            ['name' => 'companies.edit', 'section' => 'companies', 'label' => 'Modifica Società'],
            ['name' => 'companies.delete', 'section' => 'companies', 'label' => 'Elimina Società'],

            // Officers
            ['name' => 'officers.view', 'section' => 'officers', 'label' => 'Visualizza Cariche'],
            ['name' => 'officers.manage', 'section' => 'officers', 'label' => 'Gestisci Cariche'],

            // Shareholders
            ['name' => 'shareholders.view', 'section' => 'shareholders', 'label' => 'Visualizza Soci'],
            ['name' => 'shareholders.manage', 'section' => 'shareholders', 'label' => 'Gestisci Soci'],

            // Documents
            ['name' => 'documents.view', 'section' => 'documents', 'label' => 'Visualizza Documenti'],
            ['name' => 'documents.upload', 'section' => 'documents', 'label' => 'Carica Documenti'],
            ['name' => 'documents.download', 'section' => 'documents', 'label' => 'Scarica Documenti'],
            ['name' => 'documents.delete', 'section' => 'documents', 'label' => 'Elimina Documenti'],

            // Reports
            ['name' => 'reports.view', 'section' => 'reports', 'label' => 'Visualizza Report'],

            // Users
            ['name' => 'users.manage', 'section' => 'users', 'label' => 'Gestisci Utenti'],

            // Activity Log
            ['name' => 'activity_log.view', 'section' => 'activity_log', 'label' => 'Visualizza Log Attività'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }
    }
}
