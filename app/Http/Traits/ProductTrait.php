<?php

namespace App\Http\Traits;

use App\Models\HistoryProduct;
use App\Models\InventoryDiscount;
use App\Models\Product;

trait ProductTrait
{
    use SaleTrait;
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
        return Product::where('state', 1)->whereNotIn('id', $this->notAvailable())->get();
    }

    public function notAvailable(){
        return HistoryProduct::where('amount', '<=', 0)->pluck('product_id')->toArray();
    }

    public function createProduct($code, $name, $buyprice, $percent)
    {
        $saleprice = $buyprice + ($buyprice * ($percent / 100));
        $product = Product::create([
            'name' => $name,
            'code' => $code,
            'saleprice' => $saleprice,
            'state' => 1,
        ]);

        $this->addHistoryProduct($product->id, $buyprice, 0);
        return $product;
    }

    public function createProduct2($code, $name, $buyprice, $amount, $percent)
    {
        $saleprice = $buyprice + ($buyprice * ($percent / 100));
        $product = Product::create([
            'name' => $name,
            'code' => $code,
            'saleprice' => $saleprice,
            'state' => 1,
        ]);

        $this->addHistoryProduct($product->id, $buyprice, $amount);
        return $product;
    }

    public function addHistoryProduct($product_id, $buyprice, $amount)
    {
        return HistoryProduct::create([
            'product_id' => $product_id,
            'buyprice' => $buyprice,
            'amount' => $amount,
        ]);
    }

    public function discount($sale_id, $product, $amount)
    {
        $auxAmount = $amount;
        $historyProducts = $this->getHistoryProducts($product->id, 'DESC');
        foreach ($historyProducts as $historyP) {
            if ($historyP->amount >= $auxAmount) {
                $this->addExtra($sale_id, $product, $historyP->id, $auxAmount);

                $historyP->amount -= $auxAmount;
                $historyP->save();
                break;
            } else {
                $auxAmount -= $historyP->amount;
                $this->addExtra($sale_id, $product, $historyP->id, $historyP->amount);
                $historyP->amount = 0;
                $historyP->save();
            }
        }
    }

    public function addAmount($extra, $product, $amount)
    {
        $auxAmount = $amount;
        $historyProducts = $this->getHistoryProducts($product->id, 'ASC', 2);
        foreach ($historyProducts as $historyP) {
            if ($extra->history_p == $historyP->id) {
                $historyP->amount = $amount;
                $historyP->save();
            }
        }
    }

    public function addInventoryDiscount($product_id, $amount, $description, $user_id)
    {
        return InventoryDiscount::create([
            'product_id' => $product_id,
            'amount' => $amount,
            'description' => $description,
            'user_id' => $user_id
        ]);
    }

    public function getHistoryProducts($product_id, $order = "ASC", $amount = 1)
    {
        if ($amount == 1) {
            return HistoryProduct::where('product_id', $product_id)->where('amount', '>', 0)->orderBy('buyprice', $order)->get();
        } else {
            return HistoryProduct::where('product_id', $product_id)->orderBy('buyprice', $order)->get();
        }
    }

    public function getHistoryProduct($product_id)
    {
        return HistoryProduct::where('product_id', $product_id)->first();
    }

    public function getHistoryProduct2($historyP_id)
    {
        return HistoryProduct::where('id', $historyP_id)->first();
    }

    public function getAmountProduct($product_id)
    {
        return HistoryProduct::where('product_id', $product_id)->sum('amount');
    }
}
