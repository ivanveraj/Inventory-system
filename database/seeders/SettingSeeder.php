<?php

namespace Database\Seeders;

use App\Http\Traits\SettingTrait;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    use SettingTrait;
    public function run()
    {
        $this->addSetting('Time', 'PrecioMinimo', 1000);
        $this->addSetting('Time', 'TiempoMinimo', 10);
        $this->addSetting('Time', 'PrecioHoraPrincipal', 7200);
        $this->addSetting('Time', 'PorcentajeMinimoGanancia', 30);
        $this->addSetting('Time', 'PrecioHoraSecundario', 3000);
        $this->addSetting('Time', 'HoraCambio', '16:00:00');
    }
}
