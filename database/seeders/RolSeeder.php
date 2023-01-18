<?php

namespace Database\Seeders;

use App\Models\Rol;
use App\Models\RolGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        RolGroup::create([
            'name' => 'Administracion'
        ]);
        
        Rol::create([
            'name' => 'SuperAdmin',
            'state' => 1,
            'rolG_id' => 1
        ]);
        Rol::create([
            'name' => 'Administrador',
            'state' => 1,
            'rolG_id' => 1
        ]);
        Rol::create([
            'name' => 'Colaborador',
            'state' => 1,
            'rolG_id' => 1
        ]);
    }
}
