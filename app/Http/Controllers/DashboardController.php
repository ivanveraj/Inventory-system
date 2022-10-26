<?php

namespace App\Http\Controllers;

use App\Http\Traits\DashboardTrait;
use App\Models\Day;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use DashboardTrait;
    public function dashboard()
    {
        $lastFourDay = $this->getLastFourDay();
        $day = getDayCurrent();
        $total = 0;
        if (!is_null($day)) {
            $total = $day->total;
        }

        return view('dashboard', compact('lastFourDay', 'total'));
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
