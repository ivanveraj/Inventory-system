<?php

namespace App\Http\Traits;

use App\Models\HistoryProductSale;
use App\Models\HistorySale;
use App\Models\Permission;

trait GeneralTrait
{
    public function createPermission($id, $name, $permissionG_id)
    {
        return Permission::create([
            'id' => $id,
            'name' => $name,
            'state' => 1,
            'permissionG_id' => $permissionG_id
        ]);
    }
    public function createHistorySale($sale,$total, $price_time, $time)
    {
        return HistorySale::create([
            'sale'=>$sale,
            'total' => $total,
            'price_time' => $price_time,
            'time' => $time
        ]);
    }
    public function createHistoryProductSale($sale, $product_id, $amount, $price)
    {
        return HistoryProductSale::create([
            'sale' => $sale,
            'product_id' => $product_id,
            'amount' => $amount,
            'price' => $price
        ]);
    }
}
