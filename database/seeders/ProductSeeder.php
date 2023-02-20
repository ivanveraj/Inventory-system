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
        $this->createProduct2('CB', 'Club Colombia | Budweiser | Stella | Heineken', 3200,  100,30);
        $this->createProduct2('CV', 'Poker', 3200,  100,30,30);
        $this->createProduct2('COR', 'Coronita', 3200,  100,30,30);
        $this->createProduct2('CR', 'Corona', 3200,  100,30,30);
        $this->createProduct2('AL', 'Aguila Light | Negra', 3200,  100,30,30);
        $this->createProduct2('RM', 'Ron Media', 3200,  100,30);
        $this->createProduct2('RL', 'Ron Litro', 3200,  100,30);
        $this->createProduct2('RG', 'Ron Gafarra', 3200,  100,30);
        $this->createProduct2('AGM', 'Aguardiente Media', 3200,  100,30);
        $this->createProduct2('AGL', 'Aguardiente Litro', 3200,  100,30);
        $this->createProduct2('AGG', 'Aguardiente Garrafa', 3200,  100,30);
        $this->createProduct2('HT', 'Jugo Hit', 3200,  100,30);
        $this->createProduct2('HG', 'Hit Litro', 3200,  100,30);
        $this->createProduct2('GT', 'Gatorade | Squash', 3200,  100,30);
        $this->createProduct2('RB', 'Red Bull', 3200,  100,30);
        $this->createProduct2('FL', 'Four Loko', 3200,  100,30);
        $this->createProduct2('SD', 'Soda', 3200,  100,30);
        $this->createProduct2('CG', 'Cigarrillo', 3200,  100,30);
        $this->createProduct2('CP', 'Cola & Pola', 3200,  100,30);
        $this->createProduct2('AU', 'Agua Grande', 3200,  100,30);
        $this->createProduct2('PN', 'Pony Malta', 3200,  100,30);
        $this->createProduct2('GS', 'Gaseosas', 3200,  100,30);
        $this->createProduct2('PG', 'Detodito | Choclito | Dorito G', 3200,  100,30);
        $this->createProduct2('PM', 'Detodito | Dorito Med', 3200,  100,30);
        $this->createProduct2('PP', 'Detodito | Dorito Peq', 3200,  100,30);
        $this->createProduct2('MA', 'Margarita Pollo-Limon-Dorito', 3200,  100,30);
        $this->createProduct2('CHE', 'Cheetos-Choclitos-| Boliqueso', 3200,  100,30);
        $this->createProduct2('MM', 'Mani moto', 3200,  100,30);
        $this->createProduct2('TD', 'Trident', 3200,  100,30);
        $this->createProduct2('BB', 'Bom Bom Bum', 3200,  100,30);
        $this->createProduct2('HS', 'Halls', 3200,  100,30);
        $this->createProduct2('GY', 'Guantes', 3200,  100,30);
    }
}
