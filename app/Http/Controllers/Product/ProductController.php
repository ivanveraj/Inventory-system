<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Traits\ProductTrait;
use App\Models\Product;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    use ProductTrait;
    public function index()
    {
        return view('products.index_products');
    }

    public function list(Request $rq)
    {
        if ($rq->active == 0) {
            $products = Product::where('state', 1)->get();
        } else {
            $products = Product::all();
        }
        return DataTables::of($products)
            ->addColumn('name', function ($product) {
                return $product->name . " ($product->code)";
            })
            ->addColumn('amount', function ($product) {
                return '<button onclick="addStock(' . $product->id . ')" class="btn btn-primary btn-sm" data-toggle="tooltip" data-placement="top" title="Añadir stock">' . $product->amount . '</button>';
            })
            ->addColumn('buyprice', function ($product) {
                return formatMoney($product->buyprice);
            })
            ->addColumn('saleprice', function ($product) {
                return formatMoney($product->saleprice);
            })
            ->addColumn('state', function ($product) {
                $state = $product->state == 1 ? '<span
                class="badge rounded-pill bg-success">Activo</span>' : '<span
                class="badge rounded-pill bg-danger">Inactivo</span>';
                return $state;
            })
            ->addColumn('actions', function ($product) {
                $Edit =  '<button onclick="edit(' . $product->id . ')" class="btn btn-primary btn-sm" data-toggle="tooltip" data-placement="top" title="Editar">
                <i class="fas fa-edit"></i></button>';
                $icon = $product->state == 1 ? '<i class="fas fa-trash"></i>' : '<i class="fas fa-sync-alt"></i>';
                $text = $product->state == 1 ? 'Archivar' : 'Activar';
                $Archive =  '<button onclick="archive(' . $product->id . ',' . $product->state . ')" class="btn btn-primary btn-sm ml-2" data-toggle="tooltip" data-placement="top" title="' . $text . '">' . $icon . '</button>';
                return "$Edit $Archive";
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
            'saleprice' => 'required|integer|min:0'
        ]);

        $this->createProduct(strtoupper($rq->code), $rq->name, $rq->buyprice, $rq->saleprice, 0);

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
            'buyprice' => 'required|integer|min:0',
            'saleprice' => 'required|integer|min:0'
        ]);

        $product = $this->getProduct($rq->id);
        if (is_null($product)) {
            return AccionIncorrecta('', '');
        }

        $product->name = $rq->name;
        $product->code = $rq->code;
        $product->buyprice = $rq->buyprice;
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

        return view('products.add_stock', compact('product'));
    }

    public function saveStock(Request $rq)
    {
        $rq->validate([
            'id' => 'required',
            'amount' => 'required|integer|min:1'
        ]);

        $product = $this->getProduct($rq->id);
        if (is_null($product)) {
            return AccionIncorrecta('', '');
        }

        $product->amount += $rq->amount;
        $product->save();

        return AccionCorrecta('', '');
    }
}
