<?php

namespace App\Http\Traits;

use App\Models\HistoryProduct;
use App\Models\InventoryDiscount;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

trait ProductTrait
{
    use SaleTrait;
    public function getProduct($id)
    {
        return Product::where('id', $id)->first();
    }

    public function getProductActive($id)
    {
        return Product::where('id', $id)->where('is_activated', 1)->first();
    }

    public function getProducts()
    {
        return Product::where('is_activated', 1)->get();
    }


    public function availableProducts()
    {
        $array['products'] = HistoryProduct::select("product_id", DB::raw("sum(amount)"))->where('amount', '>', 0)->groupBy('product_id')->pluck('sum', 'product_id')->toArray();
        $array['avaliable'] = array_keys($array['products']);
        return $array;
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

    public function discount($product, $amount)
    {
        $product->amount -= $amount;
        $product->save();
    }

    public function addAmount($product, $amount)
    {
       $product->amount += $amount;
       $product->save();
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
