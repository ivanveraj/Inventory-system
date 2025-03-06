<?php

namespace Database\Seeders;

use App\Enums\ProductCategory;
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
        $products = [
            ['CB', 'Club Colombia | Budweiser | Stella | Heineken', ProductCategory::CERVEZA],
            ['CV', 'Poker', ProductCategory::CERVEZA],
            ['COR', 'Coronita', ProductCategory::CERVEZA],
            ['CR', 'Corona', ProductCategory::CERVEZA],
            ['AL', 'Aguila Light | Negra', ProductCategory::CERVEZA],

            ['RM', 'Ron Media', ProductCategory::LICOR],
            ['RL', 'Ron Litro', ProductCategory::LICOR],
            ['RG', 'Ron Gafarra', ProductCategory::LICOR],
            ['AGM', 'Aguardiente Media', ProductCategory::LICOR],
            ['AGL', 'Aguardiente Litro', ProductCategory::LICOR],
            ['AGG', 'Aguardiente Garrafa', ProductCategory::LICOR],

            ['HT', 'Jugo Hit', ProductCategory::BEBIDA],
            ['HG', 'Hit Litro', ProductCategory::BEBIDA],
            ['GT', 'Gatorade | Squash', ProductCategory::BEBIDA],
            ['RB', 'Red Bull', ProductCategory::BEBIDA],
            ['FL', 'Four Loko', ProductCategory::BEBIDA],
            ['SD', 'Soda', ProductCategory::BEBIDA],
            ['AU', 'Agua Grande', ProductCategory::BEBIDA],
            ['PN', 'Pony Malta', ProductCategory::BEBIDA],
            ['GS', 'Gaseosas', ProductCategory::BEBIDA],
            ['CG', 'Cigarrillo', ProductCategory::OTROS],
            ['CP', 'Cola & Pola', ProductCategory::OTROS],
            ['PG', 'Detodito | Choclito | Dorito G', ProductCategory::MECATO],
            ['PM', 'Detodito | Dorito Med', ProductCategory::MECATO],
            ['PP', 'Detodito | Dorito Peq', ProductCategory::MECATO],
            ['MA', 'Margarita Pollo-Limon-Dorito', ProductCategory::MECATO],
            ['CHE', 'Cheetos-Choclitos-| Boliqueso', ProductCategory::MECATO],
            ['MM', 'Mani moto', ProductCategory::MECATO],
            ['TD', 'Trident', ProductCategory::MECATO],
            ['BB', 'Bom Bom Bum', ProductCategory::MECATO],
            ['HS', 'Halls', ProductCategory::MECATO],

            ['GY', 'Guantes', ProductCategory::OTROS],
        ];

        foreach ($products as $product) {
            Product::create([
                'sku' => $product[0],
                'name' => $product[1],
                'description' => null,
                'category' => $product[2], // Ahora usa el enum correcto
                'barcode' => null,
                'keywords' => strtolower($product[1]),
                'buyprice' => 3200,
                'saleprice' => 4000,
                'amount' => 100,
                'discount' => 0,
                'discount_to' => null,
                'iva' => 0,
                'is_activated' => true,
                'has_stock_alert' => false,
                'min_stock_alert' => 10
            ]);
        }
    }
}
