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
        User::create([
            'name' => 'SuperAdmin',
            'user' => 'SuperAdmin',
            'rol_id' => 1,
            'state' => 1,
            'password' => Hash::make('Jivjmmm08@')
        ]);

        User::create([
            'name' => 'SuperAdmin',
            'user' => 'admin',
            'rol_id' => 1,
            'state' => 1,
            'password' => Hash::make('Qazxcv08@')
        ]);

        User::create([
            'name' => 'Administrador Yosmel',
            'user' => 'admin_yosmel',
            'rol_id' => 2,
            'state' => 1,
            'password' => Hash::make('1845')
        ]);

        User::create([
            'name' => 'Francisco Parada',
            'user' => 'admin_francisco',
            'rol_id' => 2,
            'state' => 1,
            'password' => Hash::make('Qazxcv08@')
        ]);
    }
}
