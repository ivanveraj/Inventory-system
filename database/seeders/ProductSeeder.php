<?php

namespace Database\Seeders;

use App\Http\Traits\ProductTrait;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    use ProductTrait;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $this->createProduct('CB', 'Club Colombia | Budweiser | Stella | Heineken', 3200, 4000, 100);
        $this->createProduct('CV', 'Poker', 3200, 4000, 100);
        $this->createProduct('COR', 'Coronita', 3200, 4000, 100);
        $this->createProduct('CR', 'Corona', 3200, 4000, 100);
        $this->createProduct('AL', 'Aguila Light | Negra', 3200, 4000, 100);
        $this->createProduct('RM', 'Ron Media', 3200, 4000, 100);
        $this->createProduct('RL', 'Ron Litro', 3200, 4000, 100);
        $this->createProduct('RG', 'Ron Gafarra', 3200, 4000, 100);
        $this->createProduct('AGM', 'Aguardiente Media', 3200, 4000, 100);
        $this->createProduct('AGL', 'Aguardiente Litro', 3200, 4000, 100);
        $this->createProduct('AGG', 'Aguardiente Garrafa', 3200, 4000, 100);
        $this->createProduct('HT', 'Jugo Hit', 3200, 4000, 100);
        $this->createProduct('HG', 'Hit Litro', 3200, 4000, 100);
        $this->createProduct('GT', 'Gatorade | Squash', 3200, 4000, 100);
        $this->createProduct('RB', 'Red Bull', 3200, 4000, 100);
        $this->createProduct('FL', 'Four Loko', 3200, 4000, 100);
        $this->createProduct('SD', 'Soda', 3200, 4000, 100);
        $this->createProduct('CG', 'Cigarrillo', 3200, 4000, 100);
        $this->createProduct('CP', 'Cola & Pola', 3200, 4000, 100);
        $this->createProduct('AU', 'Agua Grande', 3200, 4000, 100);
        $this->createProduct('PN', 'Pony Malta', 3200, 4000, 100);
        $this->createProduct('GS', 'Gaseosas', 3200, 4000, 100);
        $this->createProduct('PG', 'Detodito | Choclito | Dorito G', 3200, 4000, 100);
        $this->createProduct('PM', 'Detodito | Dorito Med', 3200, 4000, 100);
        $this->createProduct('PP', 'Detodito | Dorito Peq', 3200, 4000, 100);
        $this->createProduct('MA', 'Margarita Pollo-Limon-Dorito', 3200, 4000, 100);
        $this->createProduct('CHE', 'Cheetos-Choclitos-| Boliqueso', 3200, 4000, 100);
        $this->createProduct('MM', 'Mani moto', 3200, 4000, 100);
        $this->createProduct('TD', 'Trident', 3200, 4000, 100);
        $this->createProduct('BB', 'Bom Bom Bum', 3200, 4000, 100);
        $this->createProduct('HS', 'Halls', 3200, 4000, 100);
        $this->createProduct('GY', 'Guantes', 3200, 4000, 100);
    }
}
