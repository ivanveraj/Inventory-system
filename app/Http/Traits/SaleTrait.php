<?php

namespace App\Http\Traits;

use App\Models\Day;
use App\Models\Extra;
use App\Models\SaleTable;
use App\Models\Table;

trait SaleTrait
{
    use SettingTrait;
    public function getSale($id)
    {
        return SaleTable::where('id', $id)->first();
    }

    public function getSales($state, $type)
    {
        return SaleTable::where('state', $state)->get();
    }
    public function getSalesType($type)
    {
        return SaleTable::where('type', $type)->get();
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

    public function calculateTotal($sale, $minPrice = null, $minTime = null, $priceXHora = null)
    {
        $extras = $sale->extras;
        $total = 0;

        if (!is_null($sale->start_time)) {
            $time = DateDifference(now(), $sale->start_time);
            $total = ($time < $minTime) ? $minPrice : round(($priceXHora / 60) * $time);
        }

        // Sumar el total de los extras
        $total += $extras->sum('total');
        return '$' . number_format($total, 0);
    }

    public function endSale($sale)
    {
        $total = 0;
        if (!is_null($sale->start_time)) {
            $TiempoMinimo = $this->getSetting('TiempoMinimo');
            $time = DateDifference(date('Y-m-d H:i:s'), $sale->start_time);
            if ($time < $TiempoMinimo) {
                $total = $this->getSetting('PrecioMinimo');
            } else {
                $PrecioXHora = $this->getSetting('PrecioXHora');
                $total = round(($PrecioXHora / 60) * $time);
            }
        }

        if ($sale->type == 2) {
            $total = 0;
        }

        $extras = $sale->Extras;
        foreach ($extras as $ext) {
            $total += $ext->total;
        }

        $day = getDay();
        $day->total += $total;
        $day->save();

        if ($sale->type == 1) {
            $this->deleteSaleAllTable($sale);
        } else {
            $this->deleteSaleAll($sale);
        }

        $this->customNotification('success', 'Éxito', 'La venta se finalizó correctamente.');
    }
}
