<?php

namespace App\Http\Traits;

use App\Models\Day;
use App\Models\Extra;
use App\Models\SaleTable;
use App\Models\Table;

trait SaleTrait
{
    public function getSale($id)
    {
        return SaleTable::where('id', $id)->first();
    }

    public function getSales($state, $type)
    {
        return SaleTable::where('state', $state)->orderBy('id', 'ASC')->get();
    }
    public function getSalesType($type)
    {
        return SaleTable::where('type', $type)->orderBy('id', 'ASC')->get();
    }

    public function createSaleTable($table_id, $start_time, $state, $type, $client)
    {
        return SaleTable::create([
            'table_id' => $table_id,
            'start_time' => $start_time,
            'state' => $state,
            'type' => $type,
            'client' => $client
        ]);
    }
    public function getExtra($sale_id, $product_id)
    {
        return Extra::where('sale_id', $sale_id)->where('product_id', $product_id)->first();
    }
    public function getExtraById($id)
    {
        return Extra::where('id', $id)->first();
    }
    public function addExtra($sale_id, $product, $amount)
    {
        $extra = $this->getExtra($sale_id, $product->id);
        if (is_null($extra)) {
            Extra::create([
                'sale_id' => $sale_id,
                'product_id' => $product->id,
                'price' => $product->saleprice,
                'amount' => $amount,
                'total' => $product->saleprice * $amount
            ]);
        } else {
            $extra->amount += $amount;
            $extra->total = $extra->amount * $extra->price;
            $extra->save();
        }
    }
    public function changeAmount($extra, $amount)
    {
        $extra->amount = $amount;
        $extra->total = $extra->price * $amount;
        $extra->save();
    }
    public function deleteSaleAll($sale)
    {
        Extra::where('sale_id', $sale->id)->delete();
        $sale->delete();
    }

    public function deleteSaleTable($table_id)
    {
        $sale = SaleTable::where('table_id', $table_id)->first();
        if (!is_null($sale)) {
            $sale->delete();
        }
    }

    public function deleteSaleAllTable($sale)
    {
        Extra::where('sale_id', $sale->id)->delete();
        $sale->start_time = null;
        $sale->save();
    }

    public function changeClient($sale, $client)
    {
        $sale->client = $client;
        $sale->save();
    }
    public function changeDay($total)
    {
        $day = getDay();
        $day = Day::where('id', $day)->first();
        $day->total += $total;
        $day->save();
    }
}
