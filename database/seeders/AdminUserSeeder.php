<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Seed the default admin user.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@gruppodimartino.it'],
            [
                'name' => 'Amministratore',
                'password' => Hash::make(env('ADMIN_DEFAULT_PASSWORD', 'Admin@2024!Secure')),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );

        $adminRole = Role::where('name', 'admin')->first();

        if ($adminRole && !$admin->roles()->where('role_id', $adminRole->id)->exists()) {
            $admin->roles()->attach($adminRole);
        }
    }
}
