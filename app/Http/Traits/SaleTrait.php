<?php

namespace App\Http\Traits;

use App\Models\Day;
use App\Models\Extra;
use App\Models\ExtraHasHistoryProduct;
use App\Models\SaleTable;
use App\Models\Table;
use Illuminate\Support\Facades\DB;

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
    public function getExtra($sale_id, $product_id, $historyP_id)
    {
        return Extra::where('sale_id', $sale_id)->where('product_id', $product_id)->where('history_p', $historyP_id)->first();
    }

    public function getExtras($sale_id, $product_id)
    {
        return Extra::where('sale_id', $sale_id)->where('product_id', $product_id)->orderBy('created_at', 'DESC')->get();
    }

    public function getLastExtra($sale_id, $product_id)
    {
        return Extra::where('sale_id', $sale_id)->where('product_id', $product_id)->orderBy('created_at', 'DESC')->first();
    }

    public function getExtraById($id)
    {
        return Extra::where('id', $id)->first();
    }
    public function addExtra($sale_id, $product, $historyP_id, $amount)
    {
        $extra = $this->getExtra($sale_id, $product->id, $historyP_id);
        if (is_null($extra)) {
            $extra = Extra::create([
                'sale_id' => $sale_id,
                'name' => $product->name,
                'product_id' => $product->id,
                'history_p' => $historyP_id,
                'amount' => $amount
            ]);
        } else {
            $extra->amount += $amount;
            $extra->save();
        }
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

    public function getExtrasSale($sale_id)
    {
        return DB::table('extras')->select('extras.*', 'products.name', 'products.saleprice', 'history_products.buyprice')
            ->leftJoin('products', 'extras.product_id', '=', 'products.id')
            ->leftJoin('history_products', 'extras.history_p', '=', 'history_products.id')
            ->where('sale_id', $sale_id)->get();
    }

    public function getTotalSale($sale_id)
    {
        $total = 0;
        foreach ($this->getExtrasSale($sale_id) as $extra) {
            $total += $extra->saleprice * $extra->amount;
        }
        return $total;
    }
}
