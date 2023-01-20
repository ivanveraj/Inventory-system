<?php

namespace App\Http\Traits;

use App\Models\Day;

trait DashboardTrait
{
    public function getLastFourDay()
    {
        return Day::whereNotNull('finish_day')->orderBy('created_at', 'DESC')->limit(7)->get();
    }
    public function getLastDay()
    {
        return Day::whereNotNull('finish_day')->orderBy('created_at', 'DESC')->first();
    }
}
