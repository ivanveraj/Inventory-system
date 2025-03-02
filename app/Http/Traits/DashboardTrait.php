<?php

namespace App\Http\Traits;

use App\Models\Day;

trait DashboardTrait
{
    public function getLastFourDay()
    {
        return Day::whereNotNull('finish_day')->orderBy('created_at', 'DESC')->limit(4)->get();
    }
}
