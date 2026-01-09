<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => 'SuperAdmin',
            'username' => 'superadmin',
            'password' => Hash::make('Qazxcv08@')
        ]);

        $user->assignRole('SuperAdmin');

        $admin = User::create([
            'name' => 'Administrador',
            'username' => 'administrador.zona8',
            'password' => Hash::make('Qazxcv08@')
        ]);

        $admin->assignRole('Administrador');
    }
}
