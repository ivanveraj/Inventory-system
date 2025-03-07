<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Traits\ProductTrait;
use App\Http\Traits\SettingTrait;
use App\Models\HistoryProduct;
use App\Models\InventoryDiscount;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    use ProductTrait, SettingTrait;
    public function index()
    {
        return view('products.index_products');
    }

    public function list(Request $rq)
    {
        $products = Product::orderBy('updated_at', 'DESC')->orderBy('created_at', 'DESC')->get();
        $LogUser = Auth::user();
        return DataTables::of($products)
            ->addColumn('name', function ($product) {
                return '
                <div class="flex justify-start">
                    <div class="flex flex-col justify-center text-left">
                        <p class="mb-0 text-sm font-bold">Prod: ' . $product->name . '</p>
                        <p class="mb-0 text-xs">Codigo: ' . $product->code . '</p>
                    </div>
                </div>';
            })
            ->addColumn('amount', function ($product) {
                $amount = $this->getAmountProduct($product->id);
                return '<button onclick="addStock(' . $product->id . ')" class="btn bg-primary text-white btn-sm font-extrabold" data-toggle="tooltip" data-placement="top" title="Añadir stock">' . $amount . '</button>';
            })
            ->addColumn('saleprice', function ($product) {
                return '$' . formatMoney($product->saleprice);
            })
            ->addColumn('state', function ($product) {
                $state = $product->state == 1 ? '<span
                class="badge rounded-pill bg-success" style="font-size:14px">Activo</span>' : '<span
                class="badge rounded-pill bg-danger" style="font-size:14px">Inactivo</span>';
                return $state;
            })
            ->addColumn('actions', function ($product) use ($LogUser) {
                $Edit =  '<button onclick="edit(' . $product->id . ')" class="dropdown-item">Editar</button>';
                $text = $product->state == 1 ? 'Archivar' : 'Activar';
                $Archive =  '<button onclick="archive(' . $product->id . ',' . $product->state . ')" class="dropdown-item">' . $text . '</button>';
                $deleteStock = $this->getAmountProduct($product->id) <= 0 ? '' : '<button onclick="deleteStock(' . $product->id . ')" class="dropdown-item">Eliminar stock</button>';

                return '
                    <div class="btn-group dropstart">
                        <button type="button" class="btn bg-primary text-white dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu">
                          ' . $Edit . '
                          ' . $Archive . ' 
                        ' . $deleteStock . '
                        </ul>
                    </div>';
            })
            ->rawColumns(['name', 'amount', 'buyprice', 'saleprice', 'state', 'actions'])->make(true);
    }

    public function create(Request $rq)
    {
        return view('products.create_product');
    }

    public function store(Request $rq)
    {
        $rq->validate([
            'name' => 'required|string',
            'code' => 'required|string',
            'buyprice' => 'required|integer|min:0',
        ]);

        $percent = $this->getSetting('PorcentajeMinimoGanancia');
        $this->createProduct(strtoupper($rq->code), $rq->name, $rq->buyprice, 0, $percent);

        return AccionCorrecta('', '');
    }

    public function show($id)
    {
        $product = $this->getProduct($id);
        if (is_null($product)) {
            return AccionIncorrecta('', '');
        }

        return view('products.edit_product', compact('product'));
    }

    public function update(Request $rq)
    {
        $rq->validate([
            'id' => 'required',
            'name' => 'required|string',
            'code' => 'required|string',
            'saleprice' => 'required|integer|min:0'
        ]);

        $product = $this->getProduct($rq->id);
        if (is_null($product)) {
            return AccionIncorrecta('', '');
        }

        $product->name = $rq->name;
        $product->code = strtoupper($rq->code);
        $product->saleprice = $rq->saleprice;
        $product->save();

        return AccionCorrecta('', '');
    }

    public function archive(Request $rq)
    {
        $product = $this->getProduct($rq->id);
        if (is_null($product)) {
            return AccionIncorrecta('', '');
        }

        $product->state = $product->state == 1 ? 0 : 1;
        $product->save();

        return AccionCorrecta('', '');
    }


    public function addStock($id)
    {
        $product = $this->getProduct($id);
        if (is_null($product)) {
            return AccionIncorrecta('', '');
        }

        $buyprice = 0;
        $historyProducts = HistoryProduct::where('product_id', $product->id)->where('amount', '>', 0)->get();
        foreach ($historyProducts as $historyP) {
            if ($historyP->buyprice > $buyprice) {
                $buyprice = $historyP->buyprice;
            }
        }

        return view('products.add_stock', compact('product', 'historyProducts', 'buyprice'));
    }

    public function saveStock(Request $rq)
    {
        $rq->validate([
            'id' => 'required',
            'amount' => 'required|integer|min:1',
            'buyprice' => 'required|integer|min:0'
        ]);


        $product = $this->getProduct($rq->id);
        if (is_null($product)) {
            return AccionIncorrecta('', '');
        }

        $flag = false;
        $historyProducts = HistoryProduct::where('product_id', $product->id)->get();
        foreach ($historyProducts as $historyP) {
            if ($historyP->buyprice == $rq->buyprice) {
                $historyP->amount += $rq->amount;
                $historyP->save();
                $flag = true;
                break;
            }
        }

        if ($flag == false) {
            $this->addHistoryProduct($product->id, $rq->buyprice, $rq->amount);
        }

        return AccionCorrecta('', '');
    }

    public function deleteStock($id)
    {
        $product = $this->getProduct($id);
        if (is_null($product)) {
            return AccionIncorrecta('', '');
        }

        return view('products.delete_stock', compact('product'));
    }

    public function saveDeleteStock(Request $rq)
    {
        $rq->validate([
            'id' => 'required',
            'amount' => 'required|integer'
        ]);

        $LogUser = Auth::user();
        if ($LogUser->id != 1) {
            $rq->validate([
                'description' => 'required|string|max:255'
            ]);
        }

        $product = $this->getProduct($rq->id);
        if (is_null($product)) {
            return AccionIncorrecta('', '');
        }

        $amount = intval($rq->amount);
        if ($this->getAmountProduct($product->id) < $amount) {
            return AccionIncorrecta('', 'Este producto no tiene existencias en el inventario');
        }

        $historyProducts = HistoryProduct::where('product_id', $product->id)->where('amount', '>', 0)->orderBy('buyprice', 'ASC')->get();
        foreach ($historyProducts as $historyP) {
            if ($amount <= 0) {
                break;
            } else {
                if ($historyP->amount < $amount) {
                    $amount -= $historyP->amount;
                    $historyP->amount = 0;
                } else {
                    $historyP->amount =  $historyP->amount - $amount;
                    $amount = 0;
                }
                $historyP->save();
            }
        }

        if ($LogUser->id != 1) {
            $this->addInventoryDiscount($product->id, $rq->amount, $rq->description, $LogUser->id);
        }

        return AccionCorrecta('', '');
    }

    public function inventoryDiscount()
    {
        $inventoryDiscount = InventoryDiscount::orderBy('created_at', 'DESC')->get();
        return view('history.discount_product', compact('inventoryDiscount'));
    }
}
