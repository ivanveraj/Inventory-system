<?php

namespace App\Http\Traits;

use App\Enums\PaymentMethods;
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
        $productsTotal = 0;
        foreach ($this->getExtrasSale($sale->id) as $extra) {
            $productsTotal += $extra->saleprice * $extra->amount;
        }

        $timeCharge = $this->resolveTimeCharge($sale, $productsTotal);

        return $timeCharge['price_time'] + $productsTotal;
    }

    public function calculateTotal($sale)
    {
        $productsTotal = $sale->extras->sum('total');
        $timeCharge = $this->resolveTimeCharge($sale, $productsTotal);
        $total = $timeCharge['price_time'] + $productsTotal;

        return '$' . number_format($total, 0);
    }

    /**
     * Calcula duración y cobro de tiempo. Si productos >= MontoGratisTiempo, no cobra tiempo.
     *
     * @return array{time: int|float, real_time: int|float, price_time: float|int, min_time_applied: bool, min_time_value: mixed, time_free: bool}
     */
    public function resolveTimeCharge($sale, $productsTotal = null): array
    {
        $result = [
            'time' => 0,
            'real_time' => 0,
            'price_time' => 0,
            'min_time_applied' => false,
            'min_time_value' => 0,
            'time_free' => false,
        ];

        if (is_null($sale->start_time) || (int) ($sale->type ?? 1) !== 1 || !$sale->table?->usesTime()) {
            return $result;
        }

        if ($productsTotal === null) {
            $productsTotal = $sale->Extras->sum('total');
        }

        $realTime = DateDifference(date('Y-m-d H:i:s'), $sale->start_time);
        $result['real_time'] = $realTime;
        $result['time'] = $realTime;

        $threshold = (float) $this->getSetting('MontoGratisTiempo');
        if ($threshold > 0 && (float) $productsTotal >= $threshold) {
            $result['time_free'] = true;

            return $result;
        }

        $tiempoMinimo = $this->getSetting('TiempoMinimo');
        $result['min_time_value'] = $tiempoMinimo;

        if ($realTime < $tiempoMinimo) {
            $result['price_time'] = $this->getSetting('PrecioMinimo');
            $result['time'] = $tiempoMinimo;
            $result['min_time_applied'] = true;
        } else {
            $result['price_time'] = round(($this->getPrecioActual() / 60) * $realTime);
        }

        return $result;
    }

    public function endSale($sale): array
    {
        $profit = 0;
        $productsTotal = $sale->Extras->sum('total');
        $timeCharge = $this->resolveTimeCharge($sale, $productsTotal);

        $time = $timeCharge['time'];
        $realTime = $timeCharge['real_time'];
        $priceTime = $timeCharge['price_time'];
        $minTimeApplied = $timeCharge['min_time_applied'];
        $minTimeValue = $timeCharge['min_time_value'];
        $total = $priceTime;

        $client = ($sale->type == 1) ? ($sale->Table ? $sale->Table->name : 'Mesa X') : ($sale->client ?? 'Sin nombre');
        $historySale = $this->createHistorySale($client, 0, $priceTime, $time, Auth::id());

        // Preparar items para el recibo
        $items = [];
        foreach ($sale->Extras as $extra) {
            $total += $extra->total;
            $profit += ($extra->product->saleprice - $extra->product->buyprice) * $extra->amount;
            $this->createHistoryProductSale($historySale->id, $extra->product_id, $extra->amount, $extra->product->saleprice);

            $items[] = [
                'name' => $extra->name,
                'amount' => $extra->amount,
                'price' => $extra->price,
                'total' => $extra->total,
            ];
        }

        $profit += $priceTime;

        // Actualizar las ganancias del día
        $day = getDay();
        $day->total += $total;
        $day->profit += $profit;
        $day->save();

        $historySale->total = $total;
        $historySale->save();

        // Datos del recibo antes de eliminar
        $receiptData = [
            'sale_id' => $sale->id,
            'history_sale_id' => $historySale->id,
            'client' => $client,
            'payment_method' => PaymentMethods::from($sale->payment_method)->getLabel(),
            'time' => $time,
            'real_time' => $realTime,
            'min_time_applied' => $minTimeApplied,
            'min_time_value' => $minTimeValue,
            'price_time' => $priceTime,
            'time_free' => $timeCharge['time_free'],
            'start_time' => $sale->start_time ? \Carbon\Carbon::parse($sale->start_time)->format('d/m/Y H:i') : null,
            'end_time' => now()->format('d/m/Y H:i'),
            'items' => $items,
            'total' => $total,
            'date' => now()->format('d/m/Y H:i'),
        ];

        // Eliminar la venta y registrar historial de mesas si aplica
        if ($sale->type == 1) {
            $this->deleteSaleAllTable($sale);
            if ($sale->table?->usesTime()) {
                $this->addTimeHistoryTable($day->id, $sale->table_id, $time, $priceTime);
            }
        } else {
            $this->deleteSaleAll($sale);
        }

        // Notificar el éxito de la operación
        $this->customNotification('success', 'Éxito', 'La venta se finalizó correctamente.');

        return $receiptData;
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
