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
            'username' => 'superadmin',
            'state' => 1,
            'password' => Hash::make('Jivjmmm08@')
        ]);

        User::create([
            'name' => 'Francisco Parada',
            'username' => 'superAdminLasVegas',
            'state' => 1,
            'password' => Hash::make('Qazxcv08@')
        ]);
    }
}
