<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\RolHasPermission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolHasPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = Permission::all();
        for ($i = 0; $i < count($permissions); $i++) {
            RolHasPermission::create([
                'rol_id' => 2,
                'permission_id' => $permissions[$i]['id']
            ]);
        }
    }
}
