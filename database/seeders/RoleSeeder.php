<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Seed the roles table.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'label' => 'Amministratore',
                'description' => 'Accesso completo a tutte le funzionalità',
            ],
            [
                'name' => 'manager',
                'label' => 'Gestore',
                'description' => 'Gestione società e documenti',
            ],
            [
                'name' => 'viewer',
                'label' => 'Visualizzatore',
                'description' => 'Solo visualizzazione dei dati',
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['name' => $role['name']],
                $role
            );
        }
    }
}
