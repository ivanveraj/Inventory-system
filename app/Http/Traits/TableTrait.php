<?php

namespace App\Http\Traits;

use App\Models\SaleTable;
use App\Models\Table;

trait TableTrait
{
    public function getTable($id)
    {
        return Table::where('id', $id)->first();
    }

    public function getTables()
    {
        return Table::where('state', 1)->get();
    }
    public function getSaleTables()
    {
        return SaleTable::where('state', 1)->get();
    }
    public function createTable($name)
    {
        return Table::create([
            'name' => $name,
            'state' => 1
        ]);
    }
}
