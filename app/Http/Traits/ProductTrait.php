<?php

namespace App\Http\Traits;

use App\Models\Product;

trait ProductTrait
{
    public function getProduct($id)
    {
        return Product::where('id', $id)->first();
    }
    public function getProducts()
    {
        return Product::where('state', 1)->get();
    }
    public function getProductsStock()
    {
        return Product::where('state', 1)->where('amount', '>', 0)->get();
    }
    public function createProduct($code, $name, $buyprice, $saleprice, $amount)
    {
        return Product::create([
            'name' => $name,
            'code' => $code,
            'buyprice' => $buyprice,
            'saleprice' => $saleprice,
            'state' => 1,
            'amount' => $amount
        ]);
    }
    public function discount($product, $discount)
    {
        $product->amount -= $discount;
        $product->save();
    }

    public function addAmount($product, $amount)
    {
        $product->amount += $amount;
        $product->save();
    }
}
