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
        $this->addSetting('Time', 'PrecioMinimo', 1000, 'Precio mínimo que se cobra cuando el tiempo de uso es menor al tiempo mínimo establecido. Se aplica automáticamente si el tiempo calculado es inferior al tiempo mínimo.');
        $this->addSetting('Time', 'TiempoMinimo', 10, 'Tiempo mínimo en minutos que se cobra independientemente del tiempo real de uso. Si el tiempo de uso es menor a este valor, se aplica el precio mínimo.');
        $this->addSetting('Time', 'PrecioHoraPrincipal', 7200, 'Precio por hora que se aplica durante el horario principal (desde la hora de cambio hasta las 07:00 del día siguiente). Este es el precio más alto por hora.');
        $this->addSetting('Time', 'PorcentajeMinimoGanancia', 30, 'Porcentaje mínimo de ganancia que debe tener un producto. Se utiliza para validar que los precios de venta mantengan un margen de ganancia adecuado sobre el precio de compra.');
        $this->addSetting('Time', 'PrecioHoraSecundario', 3000, 'Precio por hora que se aplica durante el horario secundario (desde las 07:00 hasta la hora de cambio). Este es el precio más bajo por hora.');
        $this->addSetting('Time', 'HoraCambio', '16:00:00', 'Hora en formato 24 horas (HH:mm:ss) en la que cambia el precio de hora secundario a hora principal. Después de esta hora se aplica el precio principal hasta las 07:00 del día siguiente.');
    }
}
