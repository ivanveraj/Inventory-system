<?php

namespace App\Http\Controllers;

use App\Http\Traits\DashboardTrait;
use App\Http\Traits\SettingTrait;
use App\Http\Traits\TableTrait;
use App\Models\Day;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use DashboardTrait, SettingTrait, TableTrait;
    public function dashboard()
    {
        $lastFourDay = $this->getLastFourDay();
        $priceTime = $this->getSetting('PrecioXHora');

        $day = getDayCurrent();
        $historyTables = null;
        $currentTTimeTable = 0;
        $currentTValueTable = 0;
        $currentSales = 0;
        if (!is_null($day)) {
            $currentSales = $day->total;
            $historyTables = $this->getHistoryTables($day->id);
            foreach ($historyTables as $historyT) {
                $saleXTable = round(($priceTime / 60) * $historyT->time);
                $historyT->total += $saleXTable;
                $currentTTimeTable += $historyT->time;
                $currentTValueTable += $saleXTable;
            }
        }

        $lastDay = $this->getLastDay();
        $lastHistoryTables = null;
        $lastTTimeTable = 0;
        $lastTValueTable = 0;
        $lastSales = 0;
        if (!is_null($lastDay)) {
            $lastSales = $lastDay->total;
            $lastHistoryTables = $this->getHistoryTables($lastDay->id);
            foreach ($lastHistoryTables as $historyT) {
                $saleXTable = round(($priceTime / 60) * $historyT->time);
                $historyT->total += $saleXTable;
                $lastTTimeTable += $historyT->time;
                $lastTValueTable += $saleXTable;
            }
        }


        return view('home.dashboard', compact(
            'lastFourDay',
            'currentTTimeTable',
            'currentTValueTable',
            'currentSales',
            'day',
            'historyTables',
            'lastDay',
            'lastHistoryTables',
            'lastTTimeTable',
            'lastTValueTable',
            'lastSales'
        ));
    }

    public function getDataSales()
    {
        $days = [];
        $saleArray = [];
        $sales = Day::whereNotNull('finish_day')->orderBy('created_at', 'DESC')->limit(6)->get();
        $sales = $sales->reverse();

        foreach ($sales as $sale) {
            $date = date('d-m-Y', strtotime($sale->created_at));
            $saleArray[] = $sale->total;
            $days[] = $date;
        }
        $array['days'] = $days;
        $array['sales'] = $saleArray;
        return AccionCorrecta('', '', 1, $array);
    }
}
