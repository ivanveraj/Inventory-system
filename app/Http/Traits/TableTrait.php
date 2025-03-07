<?php

namespace App\Http\Traits;

use App\Models\HistoryTable;
use App\Models\SaleTable;
use App\Models\Table;

trait TableTrait
{
    public function getTableX($id)
    {
        return Table::where('id', $id)->first();
    }

    public function getTables()
    {
        return Table::where('state', 1)->orderBy('id', 'ASC')->get();
    }
    public function getSaleTables()
    {
        return SaleTable::where('state', 1)->orderBy('id', 'ASC')->get();
    }
    public function createTable($name)
    {
        return Table::create([
            'name' => $name,
            'state' => 1
        ]);
    }
    public function addTimeHistoryTable($day_id, $table_id, $time)
    {
        $updated = HistoryTable::where('day_id', $day_id)->where('table_id', $table_id)->increment('time', $time);
        if (!$updated) {
            HistoryTable::create([
                'day_id' => $day_id,
                'table_id' => $table_id,
                'time' => $time
            ]);
        }
    }

    public function getHistoryTables($day_id)
    {
        return HistoryTable::where('day_id', $day_id)->orderBy('time', 'DESC')->get();
    }
}
