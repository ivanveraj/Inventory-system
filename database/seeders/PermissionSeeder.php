<?php

namespace Database\Seeders;

use App\Http\Traits\GeneralTrait;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    use GeneralTrait;
    public function run()
    {
       $this->createPermission(1,'Administración de configuraciones del sistema',1);
       $this->createPermission(2,'Administración de usuarios',1);
       $this->createPermission(3,'Administración de roles',1);
       $this->createPermission(4,'Administración de inventarios',1);
       $this->createPermission(5,'Administración de mesas',1);
    }
}
