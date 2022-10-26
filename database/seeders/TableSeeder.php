<?php

namespace Database\Seeders;

use App\Http\Traits\SaleTrait;
use App\Http\Traits\TableTrait;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TableSeeder extends Seeder
{
    use TableTrait, SaleTrait;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createTable("Mesa 1");
        $this->createSaleTable(1, null, 1, 1, null);
        $this->createTable("Mesa 2");
        $this->createSaleTable(2, null, 1, 1, null);
        $this->createTable("Mesa 3");
        $this->createSaleTable(3, null, 1, 1, null);
        $this->createTable("Mesa 4");
        $this->createSaleTable(4, null, 1, 1, null);
        $this->createTable("Mesa 5");
        $this->createSaleTable(5, null, 1, 1, null);
        $this->createTable("Mesa 6");
        $this->createSaleTable(6, null, 1, 1, null);
    }
}
