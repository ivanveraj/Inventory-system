<?php

namespace App\Http\Controllers\Sale;

use App\Http\Controllers\Controller;
use App\Http\Traits\ProductTrait;
use App\Http\Traits\SaleTrait;
use App\Http\Traits\SettingTrait;
use App\Http\Traits\TableTrait;
use App\Models\HistoryProductSale;
use App\Models\HistorySale;
use App\Models\Product;
use App\Models\SaleTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    use TableTrait, SaleTrait, ProductTrait, SettingTrait;
    public function finishDay()
    {

        $sales = SaleTable::where('type', 1)->get();
        foreach ($sales as $sale) {
            if (!is_null($sale->start_time)) {
                return AccionIncorrecta('', 'Debe cerrar todas las ventas de las mesas, para proceder');
            }
        }

        $couldGeneral = SaleTable::where('type', 2)->first();
        if (!is_null($couldGeneral)) {
            return AccionIncorrecta('', 'Debe cerrar todas las ventas generales, para proceder');
        }
        $day = getDay();
        $day->finish_day = date('Y-m-d H:i:s');
        $day->save();
        return AccionCorrecta('', '');
    }
    public function initDay()
    {
        getDay();
        return AccionCorrecta('', '');
    }
    public function index()
    {
        $day = getExistDay();
        return view('sales.index_sales', compact('day'));
    }

    //Ventas mesas
    public function tablesSales()
    {
       /*  $sales = $this->getSalesType(1);
        $TiempoMinimo = $this->getSetting('TiempoMinimo');
        $PrecioXHora = $this->getSetting('PrecioXHora');
        $PrecioMinimo = $this->getSetting('PrecioMinimo');
        return view('sales.tables_sales', compact('sales', 'TiempoMinimo', 'PrecioXHora', 'PrecioMinimo')); */
    }

    //Ventas generales
    public function generalSale()
    {
        $general = $this->getSalesType(2);
        return view('sales.general_sales', compact('general'));
    }

    public function newSaleGeneral()
    {
        $sale = $this->createSaleTable(null, null, 1, 2, null);
        return AccionCorrecta('', '');
    }

    public function addProduct(Request $rq)
    {
        $rq->validate([
            'sale_id' => 'required',
            'product_id' => 'required',
            'amount' => 'required|integer|min:1'
        ]);

        $product = $this->getProduct($rq->product_id);
        if (is_null($product)) {
            return AccionIncorrecta('', '');
        }

        $sale = $this->getSale($rq->sale_id);
        if (is_null($sale)) {
            return AccionIncorrecta('', '');
        }

        if ($product->state != 1) {
            return AccionIncorrecta('', 'No existe o se encuentra inactivo el producto');
        }

        $amount = round($rq->amount);
        if ($this->getAmountProduct($product->id) < $amount) {
            return AccionIncorrecta('', 'No existe la cantidad solicitada en el inventario');
        }


        $this->discount($sale->id, $product, $amount);


        return AccionCorrecta('', '');
    }

    public function products(Request $rq)
    {
        $avaliable = $this->availableProducts();
        if (isset($rq->searchTerm)) {
            $searchTerm = strtoupper($rq->searchTerm);
            $products = Product::where('state', 1)->whereIn('id', $avaliable['avaliable'])->where('code', 'LIKE', "%" . $searchTerm . "%")->get();
        } else {
            $products = Product::where('state', 1)->whereIn('id', $avaliable['avaliable'])->get();
        }

        $array = [];
        foreach ($products as $product) {
            $amount = isset($avaliable['products'][$product->id]) ? $avaliable['products'][$product->id] : 0;
            $array[] = ['id' => $product->id, 'text' => $product->name . " (" . $product->code . ") [" . $amount . "]"];
        }
        return response()->json($array);
    }
    public function startTime(Request $rq)
    {
        $sale = $this->getSale($rq->sale_id);
        if (is_null($sale)) {
            return AccionIncorrecta('', 'No existe ninguna venta con este identificador');
        }

        if ($sale->type != 1) {
            return AccionIncorrecta('', '');
        }

        $sale->start_time = date("Y-m-d H:i:s");
        $sale->save();
        return AccionCorrecta('', '');
    }

    public function dataGeneral(Request $rq)
    {
        $type = is_null($rq->type) ? 1 : $rq->type;
        $type = $type == 1 ? 1 : 2;

        $general = $this->getSalesType($type);
        foreach ($general as $sale) {
            $arrayE = [];
            $extras = DB::table('extras')->select('extras.*', 'products.name', 'products.saleprice')
                ->leftJoin('products', 'extras.product_id', '=', 'products.id')
                ->where('sale_id', $sale->id)->get();
            foreach ($extras as $extra) {
                if (isset($arrayE[$extra->product_id])) {
                    $arrayE[$extra->product_id]['amount'] += $extra->amount;
                } else {
                    $arrayE[$extra->product_id] = ['product_id' => $extra->product_id, 'extra_id' => $extra->id, 'name' => $extra->name, 'amount' => $extra->amount, 'price' => $extra->saleprice];
                }
            }
            $sale->ArrayExtras = $arrayE;
        }

        return response()->json(['general' => $general]);
    }

    public function changeAmountExtra(Request $rq)
    {
        if ($rq->amount < 1) {
            return AccionIncorrecta('', 'La cantidad debe ser mayor a 0');
        }
        $amount = round($rq->amount);

        $sale = $this->getSale($rq->sale_id);
        if (is_null($sale)) {
            return AccionIncorrecta('', 'No existe ninguna venta asociada a este producto extra');
        }

        $product = $this->getProduct($rq->product_id);
        if (is_null($product)) {
            return AccionIncorrecta('', 'No existe o se encuentra inactivo el producto');
        }

        $extras = $this->getExtras($sale->id, $product->id);

        $extrasAmount = $extras->sum('amount');
        $diff = $amount - $extrasAmount;
        if ($this->getAmountProduct($product->id) < $diff) {
            return AccionIncorrecta('', 'No existe la cantidad solicitada en el inventario');
        }

        $diff = abs($diff);
        if ($amount > $extrasAmount) {
            $this->discount($sale->id, $product, $diff);
        } else {
            foreach ($extras as $extra) {
                $historyP = $this->getHistoryProduct2($extra->history_p);

                if ($extra->amount >= $diff) {
                    $historyP->amount += $diff;
                    $historyP->save();

                    $extra->amount = $extra->amount - $diff;
                    $extra->save();
                    break;
                } else {

                    if (($diff - $extra->amount) <= 0) {
                        $historyP->amount += $diff;
                        $historyP->save();

                        $extra->amount -= $diff;
                        $extra->save();
                    } else {
                        $diff = $diff - $extra->amount;

                        $historyP->amount += $extra->amount;
                        $historyP->save();

                        $extra->delete();
                    }
                }
            }
        }

        $total = 0;
        foreach ($this->getExtrasSale($sale->id) as $extra) {
            $total += $extra->saleprice * $extra->amount;
        }

        return AccionCorrecta('', '', 1, $total);
    }


    public function plusExtra(Request $rq)
    {
        $sale = $this->getSale($rq->sale_id);
        if (is_null($sale)) {
            return AccionIncorrecta('', 'No existe ninguna venta asociada a este producto extra');
        }

        $product = $this->getProduct($rq->product_id);
        if (is_null($product)) {
            return AccionIncorrecta('', 'No existe o se encuentra inactivo el producto');
        }

        if ($this->getAmountProduct($product->id) - 1 < 0) {
            return AccionIncorrecta('', 'No existe la cantidad solicitada en el inventario');
        }

        $this->discount($sale->id, $product, 1);

        $extras = $this->getExtras($sale->id, $product->id);
        return AccionCorrecta('', '', 1, ['total' => $this->getTotalSale($sale), 'amount' => $extras->sum('amount')]);
    }

    public function minExtra(Request $rq)
    {
        $sale = $this->getSale($rq->sale_id);
        if (is_null($sale)) {
            return AccionIncorrecta('', 'No existe ninguna venta asociada a este producto extra');
        }

        $product = $this->getProduct($rq->product_id);
        if (is_null($product)) {
            return AccionIncorrecta('', 'No existe o se encuentra inactivo el producto');
        }

        $extra = $this->getLastExtra($sale->id, $product->id);
        if (is_null($product)) {
            return AccionIncorrecta('', 'No existe el producto extra, recarge la pagina');
        }

        $historyP = $this->getHistoryProduct2($extra->history_p);
        if (is_null($historyP)) {
            return AccionIncorrecta('', 'No existe historial del producto, recarge la pagina');
        }

        $historyP->amount++;
        $historyP->save();

        $extra->amount--;
        $extra->save();

        if ($extra->amount == 0) {
            $extra->delete();
        }

        $extras = $this->getExtras($sale->id, $product->id);

        return AccionCorrecta('', '', 1, ['total' => $this->getTotalSale($sale), 'amount' => $extras->sum('amount')]);
    }

    public function changeNameClient(Request $rq)
    {
        $sale = $this->getSale($rq->sale_id);
        if (is_null($sale)) {
            return AccionIncorrecta('', 'No existe ninguna venta con este identificador');
        }

        $this->changeClient($sale, $rq->client);

        return AccionCorrecta('', '');
    }

    public function deleteExtra(Request $rq)
    {
        $sale = $this->getSale($rq->sale_id);
        if (is_null($sale)) {
            return AccionIncorrecta('', 'No existe ninguna venta con este identificador');
        }

        $product = $this->getProduct($rq->product_id);
        if (is_null($product)) {
            return AccionIncorrecta('', 'No existe o se encuentra inactivo el producto');
        }

        $extras = $this->getExtras($sale->id, $product->id);
        foreach ($extras as $extra) {
            $historyP = $this->getHistoryProduct($product->id);
            $historyP->amount += $extra->amount;
            $historyP->save();

            $extra->delete();
        }

        return AccionCorrecta('', '');
    }

    public function detail($sale_id)
    {
        $sale = $this->getSale($sale_id);
        if (is_null($sale)) {
            return AccionIncorrecta('', 'No existe ninguna venta con este identificador');
        }

        $total = 0;
        $priceTime = 0;
        $time = "";

        if (!is_null($sale->start_time)) {
            $TiempoMinimo = $this->getSetting('TiempoMinimo');
            $time = DateDifference(date('Y-m-d H:i:s'), $sale->start_time);
            if ($time < $TiempoMinimo) {
                $total = $this->getSetting('PrecioMinimo');
            } else {
                $PrecioXHora = $this->getSetting('PrecioXHora');
                $total = round(($PrecioXHora / 60) * $time);
            }
            $priceTime = $total;
        }

        if ($sale->type == 2) {
            $total = 0;
        }


        $extras = $this->getExtrasSale($sale->id);
        $arrayE = [];
        foreach ($extras as $extra) {
            $total += $extra->saleprice * $extra->amount;
            if (isset($arrayE[$extra->product_id])) {
                $arrayE[$extra->product_id]['amount'] += $extra->amount;
            } else {
                $arrayE[$extra->product_id] = ['product_id' => $extra->product_id, 'extra_id' => $extra->id, 'name' => $extra->name, 'amount' => $extra->amount, 'price' => $extra->saleprice];
            }
        }
        $sale->ArrayExtras = $arrayE;

        return view('sales.detail', compact('total', 'sale', 'extras', 'time', 'priceTime'));
    }

    public function accountPayment(Request $rq)
    {
        $day = getDay();
        if (is_null($day)) {
            return AccionIncorrecta('', 'No existe el dia, por favor recargar');
        }

        $sale = $this->getSale($rq->sale_id);
        if (is_null($sale)) {
            return AccionIncorrecta('', 'No existe ninguna venta con este identificador');
        }

        $time = 0;
        $priceTime = 0;
        if (!is_null($sale->start_time)) {
            $TiempoMinimo = $this->getSetting('TiempoMinimo');
            $time = DateDifference(date('Y-m-d H:i:s'), $sale->start_time);
            if ($time < $TiempoMinimo) {
                $time = 10;
                $priceTime = $this->getSetting('PrecioMinimo');
            } else {
                $PrecioXHora = $this->getSetting('PrecioXHora');
                $priceTime = round(($PrecioXHora / 60) * $time);
            }
        }


        if ($sale->type == 1) {
            $table = $sale->Table;
            $client = is_null($table) ? 'Mesa X' : $table->name;
        } else {
            $priceTime = 0;
            $client = is_null($sale->client) ? 'Sin nombre' : $sale->client;
        }

        $total = $priceTime;
        $profit = $priceTime;

        $historySale = $this->createHistorySale($client, 0, $priceTime, $time, Auth::user()->id);
        foreach ($this->getExtrasSale($sale->id) as $ext) {
            $this->createHistoryProductSale($historySale->id, $ext->product_id, $ext->amount, $ext->saleprice);
            $total += $ext->saleprice * $ext->amount;
            $profit += ($ext->saleprice - $ext->buyprice) * $ext->amount;
        }

        $day->total += $total;
        $day->profit += $profit;
        $day->save();

        $historySale->total = $total;
        $historySale->save();

        if ($sale->type == 1) {
            $this->deleteSaleAllTable($sale);
            $this->addTimeHistoryTable($day->id, $sale->table_id, $time, $priceTime);
        } else {
            $this->deleteSaleAll($sale);
        }

        return AccionCorrecta('', '');
    }

    public function detail_sales()
    {
        $priceTime = $this->getSetting('PrecioXHora');
        $historySales = HistorySale::orderBy('created_at', 'DESC')->get();


        $historyProductSales = HistoryProductSale::orderBy('created_at', 'DESC')->get();
        return view('history.sale_history', compact('historySales', 'historyProductSales'));
    }

    public function histoyDetail($history_id)
    {
        $historyS = $this->getHistorySale($history_id);
        if (is_null($historyS)) {
            return AccionIncorrecta('', '');
        }

        $extras = $this->getHistoryProductsSale($history_id);
        if (is_null($extras)) {
            return AccionIncorrecta('', '');
        }

        $total = $historyS->total;

        return view('history.detail', compact('total', 'extras', 'historyS'));
    }
}
