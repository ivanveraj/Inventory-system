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
    public function createHistorySale($client, $total, $price_time, $time, $user_id)
    {
        return HistorySale::create([
            'day_id' => getDay()->id,
            'client' => $client,
            'total' => $total,
            'price_time' => $price_time,
            'time' => $time,
            'user_id' => $user_id
        ]);
    }
    public function createHistoryProductSale($history_sale, $product_id, $amount, $price)
    {
        return HistoryProductSale::create([
            'history_sale' => $history_sale,
            'product_id' => $product_id,
            'amount' => $amount,
            'price' => $price
        ]);
    }
    public function getHistorySale($history_id)
    {
        return HistorySale::where('id', $history_id)->first();
    }
    public function getHistoryProductsSale($history_id)
    {
        return HistoryProductSale::where('history_sale', $history_id)->get();
    }
}
