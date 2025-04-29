<?php

namespace App\Http\Traits;

use App\Models\Day;
use App\Models\Extra;
use App\Models\ExtraHasHistoryProduct;
use App\Models\SaleTable;
use App\Models\Table;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait SaleTrait
{
    use GeneralTrait, SettingTrait, TableTrait;
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
    public function addExtra($sale_id, $product, $amount)
    {
        $extra = $this->getExtra($sale_id, $product->id);
        if (is_null($extra)) {
            Extra::create([
                'sale_id' => $sale_id,
                'product_id' => $product->id,
                'name' => $product->name,
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

    public function getTotalSale($sale)
    {
        $total = 0;
        if ($sale->type == 1 && !is_null($sale->start_time)) {
            $time = DateDifference(date('Y-m-d H:i:s'), $sale->start_time);
            $total = ($time < $this->getSetting('TiempoMinimo')) ? $this->getSetting('PrecioMinimo') : round(($this->getPrecioActual() / 60) * $time);
        }

        foreach ($this->getExtrasSale($sale->id) as $extra) {
            $total += $extra->saleprice * $extra->amount;
        }
        return $total;
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
        $priceTime = 0;
        $profit = 0;
        $time = 0;

        // Calcular el precio basado en el tiempo si la venta es por tiempo
        if (!is_null($sale->start_time) && $sale->type == 1) {
            $TiempoMinimo = $this->getSetting('TiempoMinimo');
            $time = DateDifference(date('Y-m-d H:i:s'), $sale->start_time);

            if ($time < $TiempoMinimo) {
                $total = $this->getSetting('PrecioMinimo');
                $time = $TiempoMinimo;
            } else {
                $total = round(($this->getPrecioActual() / 60) * $time);
            }

            $priceTime = $total;
        }

        $client = ($sale->type == 1) ? ($sale->Table ? $sale->Table->name : 'Mesa X') : ($sale->client ?? 'Sin nombre');
        $historySale = $this->createHistorySale($client, 0, $priceTime, $time, Auth::id());

        // Calcular el total y la ganancia por productos extra
        foreach ($sale->Extras as $extra) {
            $total += $extra->total;
            $profit += ($extra->product->saleprice - $extra->product->buyprice) * $extra->amount;
            $this->createHistoryProductSale($historySale->id, $extra->product_id, $extra->amount, $extra->product->saleprice);
        }

        $profit += $priceTime;

        // Actualizar las ganancias del día
        $day = getDay();
        $day->total += $total;
        $day->profit += $profit;
        $day->save();

        $historySale->total = $total;
        $historySale->save();

        // Eliminar la venta y registrar historial de mesas si aplica
        if ($sale->type == 1) {
            $this->deleteSaleAllTable($sale);
            $this->addTimeHistoryTable($day->id, $sale->table_id, $time, $priceTime);
        } else {
            $this->deleteSaleAll($sale);
        }

        // Notificar el éxito de la operación
        $this->customNotification('success', 'Éxito', 'La venta se finalizó correctamente.');
    }

    public function getPrecioActual()
    {
        $now = Carbon::now();
        $horaCambio = Carbon::createFromFormat('H:i:s', $this->getSetting('HoraCambio'));
        $inicioDia = Carbon::createFromTime(7, 0, 0); // 7:00 AM

        if ($horaCambio->gt($inicioDia)) {
            // Rango cruza medianoche: de horaCambio (ej. 16:00) hasta 07:00 del día siguiente
            if ($now->gte($horaCambio) || $now->lt($inicioDia)) {
                return $this->getSetting('PrecioHoraPrincipal'); // $7200
            }
        } else {
            // Rango normal: de horaCambio a inicioDia dentro del mismo día
            if ($now->between($horaCambio, $inicioDia)) {
                return $this->getSetting('PrecioHoraPrincipal'); // $7200
            }
        }

        // En todos los demás casos, aplicar precio secundario
        return $this->getSetting('PrecioHoraSecundario'); // $3000
    }
}
