<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Setting::create([
            'group' => 'Time',
            'key' => 'PrecioMinimo',
            'value' => 1000
        ]);
        Setting::create([
            'group' => 'Time',
            'key' => 'TiempoMinimo',
            'value' => 10
        ]);
        Setting::create([
            'group' => 'Time',
            'key' => 'PrecioXHora',
            'value' => 6000
        ]);
    }
}
