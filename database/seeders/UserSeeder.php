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
            'email' => 'superadmin@gmail.com',
            'rol_id'=>1,
            'state' => 1,
            'password' => Hash::make('12345678')
        ]);
    }
}
