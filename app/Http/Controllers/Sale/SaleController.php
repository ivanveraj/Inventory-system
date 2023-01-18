<?php

namespace App\Http\Controllers\Sale;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use App\Http\Traits\ProductTrait;
use App\Http\Traits\SaleTrait;
use App\Http\Traits\SettingTrait;
use App\Http\Traits\TableTrait;
use App\Models\HistoryProductSale;
use App\Models\HistorySale;
use App\Models\Product;
use App\Models\SaleTable;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    use TableTrait, SaleTrait, ProductTrait, SettingTrait, GeneralTrait;
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
        $sales = $this->getSalesType(1);
        $TiempoMinimo = $this->getSetting('TiempoMinimo');
        $PrecioXHora = $this->getSetting('PrecioXHora');
        $PrecioMinimo = $this->getSetting('PrecioMinimo');
        return view('sales.tables_sales', compact('sales', 'TiempoMinimo', 'PrecioXHora', 'PrecioMinimo'));
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

        if ($product->state != 1 || $product->amount < $rq->amount) {
            return AccionIncorrecta('', '');
        }

        $this->addExtra($sale->id, $product, $rq->amount);
        $this->discount($product, $rq->amount);

        return AccionCorrecta('', '');
    }

    public function products(Request $rq)
    {
        if (isset($rq->searchTerm)) {
            $searchTerm = strtoupper($rq->searchTerm);
            $products = Product::where('state', 1)->where('amount', '>', 0)->where('code', 'LIKE', "%" . $searchTerm . "%")->get();
        } else {
            $products = $this->getProductsStock();
        }
        $array = [];
        foreach ($products as $product) {
            $array[] = ['id' => $product->id, 'text' => $product->name . " (" . $product->code . ") [$product->amount]"];
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
            $sale->Extras;
        }
        return response()->json(['general' => $general]);
    }

    public function changeAmountExtra(Request $rq)
    {
        $amount = is_null($rq->amount) || $rq->amount < 0 ? 0 : $rq->amount;
        $extra = $this->getExtraById($rq->extra_id);
        if (is_null($extra)) {
            return AccionIncorrecta('', 'No existe este producto en ninguna venta, recarge la pagina');
        }

        $sale = $this->getSale($extra->sale_id);
        if (is_null($sale)) {
            return AccionIncorrecta('', 'No existe ninguna venta asociada a este producto extra');
        }

        $product = $extra->Product;
        if (is_null($product)) {
            return AccionIncorrecta('', 'No existe ese producto');
        }

        /* si la cantidad que 90 y quiero quitar cervezas las cuales son 10 y quiero quitar 5 me deben quedar 95 */
        $diff = $amount - $extra->amount;

        if ($product->amount < $diff) {
            return AccionIncorrecta('', 'No existe la cantidad solicitada en el inventario');
        }
        $diff = abs($diff);
        if ($amount > $extra->amount) {
            $this->discount($product, $diff);
        } else {
            $this->addAmount($product, $diff);
        }
        $this->changeAmount($extra, $amount);
        $total = 0;
        foreach ($sale->Extras as $ext) {
            $total += $ext->total;
        }
        return AccionCorrecta('', '', 1, $total);
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
        $extra = $this->getExtraById($rq->extra_id);
        if (is_null($extra)) {
            return AccionIncorrecta('', '');
        }

        $product = $extra->Product;
        if (is_null($product)) {
            return AccionIncorrecta('', 'No existe ese producto');
        }

        $this->addAmount($product, $extra->amount);

        $extra->delete();
        return AccionCorrecta('', '');
    }

    public function detail($sale_id)
    {
        
        $sale = $this->getSale($sale_id);
        if (is_null($sale)) {
            return AccionIncorrecta('', '');
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

        $extras = $sale->Extras;
        foreach ($extras as $ext) {
            $total += $ext->total;
        }
        return view('sales.detail', compact('total', 'sale', 'extras', 'time', 'priceTime'));
    }

    public function accountPayment(Request $rq)
    {
        $rq->validate([
            'sale_id' => 'required',
        ]);

        $sale = $this->getSale($rq->sale_id);
        if (is_null($sale)) {
            return AccionIncorrecta('', '');
        }

        $time = 0;
        $priceTime = 0;
        if (!is_null($sale->start_time)) {
            $TiempoMinimo = $this->getSetting('TiempoMinimo');
            $time = DateDifference(date('Y-m-d H:i:s'), $sale->start_time);
            if ($time < $TiempoMinimo) {
                $priceTime = $this->getSetting('PrecioMinimo');
            } else {
                $PrecioXHora = $this->getSetting('PrecioXHora');
                $priceTime = round(($PrecioXHora / 60) * $time);
            }
        }

        if ($sale->type == 2) {
            $priceTime = 0;
        }

        $total = $priceTime;
        $extras = $sale->Extras;

        $next = HistoryProductSale::orderBy('id', 'desc')->first();
        $next = is_null($next) ? 1 : $next->id + 1;

        foreach ($extras as $ext) {
            $this->createHistoryProductSale($next, $ext->product_id, $ext->amount, $ext->price);
            $total += $ext->total;
        }

        $day = getDay();
        $day->total += $total;
        $day->save();

        $this->createHistorySale($next, $total, $priceTime, $time);
        if ($sale->type == 1) {
            $this->deleteSaleAllTable($sale);
        } else {
            $this->deleteSaleAll($sale);
        }

        return AccionCorrecta('', '');
    }
    public function detail_sales()
    {
        return redirect()->back();
        /*     $historyTables = HistorySale::orderBy('created_at', 'DESC')->all();
        $historyProductSales = HistoryProductSale::all(); */
        return view('history.sale_history', compact('historyTables', 'historyProductSales'));
    }
}
