<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $id = DB::table('permissions')->where('name', 'reports.view')->value('id');

        if ($id) {
            DB::table('permission_user')->where('permission_id', $id)->delete();
            DB::table('permissions')->where('id', $id)->delete();
        }
    }

    public function down(): void
    {
        DB::table('permissions')->insert([
            'name'    => 'reports.view',
            'section' => 'reports',
            'label'   => 'Visualizza Report',
        ]);
    }
};
