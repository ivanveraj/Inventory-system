<?php

namespace App\Http\Controllers\Table;

use App\Http\Controllers\Controller;
use App\Http\Traits\SaleTrait;
use App\Http\Traits\TableTrait;
use App\Models\Table;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TableController extends Controller
{
    use TableTrait, SaleTrait;
    public function index()
    {
        return view('tables.index_tables');
    }

    public function list(Request $rq)
    {
        $tables = Table::orderBy('id', 'asc')->get();

        return DataTables::of($tables)
            ->addColumn('name', function ($table) {
                return $table->name;
            })
            ->addColumn('state', function ($table) {
                $state = $table->state == 1 ? '<span
                class="badge rounded-pill bg-success">Activo</span>' : '<span
                class="badge rounded-pill bg-danger">Inactivo</span>';
                return $state;
            })
            ->addColumn('actions', function ($table) {
                $Edit =  '<button onclick="edit(' . $table->id . ')" class="btn btn-primary btn-sm" data-toggle="tooltip" data-placement="top" title="Editar">
                <i class="fas fa-edit"></i></button>';
                $icon = $table->state == 1 ? '<i class="fas fa-trash"></i>' : '<i class="fas fa-sync-alt"></i>';
                $text = $table->state == 1 ? 'Archivar' : 'Activar';
                $Archive =  '<button onclick="archive(' . $table->id . ',' . $table->state . ')" class="btn btn-primary btn-sm ml-2" data-toggle="tooltip" data-placement="top" title="' . $text . '">' . $icon . '</button>';
                return "$Edit $Archive";
            })
            ->rawColumns(['name', 'state', 'actions'])->make(true);
    }

    public function create(Request $rq)
    {
        return view('tables.create_table');
    }

    public function store(Request $rq)
    {
        $rq->validate([
            'name' => 'required|string',
        ]);

        $table = $this->createTable($rq->name);
        $this->createSaleTable($table->id, null, 1, 1, null);

        return AccionCorrecta('', '');
    }

    public function show($id)
    {
        $table = $this->getTable($id);
        if (is_null($table)) {
            return AccionIncorrecta('', '');
        }

        return view('tables.edit_table', compact('table'));
    }

    public function update(Request $rq)
    {
        $rq->validate([
            'id' => 'required',
            'name' => 'required|string'
        ]);

        $table = $this->getTable($rq->id);
        if (is_null($table)) {
            return AccionIncorrecta('', '');
        }

        $table->name = $rq->name;
        $table->save();

        return AccionCorrecta('', '');
    }

    public function archive(Request $rq)
    {
        $table = $this->getTable($rq->id);
        if (is_null($table)) {
            return AccionIncorrecta('', '');
        }

        if ($table->state == 1) {
            $table->state = 0;
            $this->deleteSaleTable($table->id);
        } else {
            $table->state = 1;
            $this->createSaleTable($table->id, null, 1, 1, null);
        }

        $table->save();

        return AccionCorrecta('', '');
    }
}
