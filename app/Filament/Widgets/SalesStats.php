<?php

namespace App\Filament\Widgets;

use App\Http\Traits\DashboardTrait;
use App\Models\Day;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SalesStats extends BaseWidget
{
    use DashboardTrait;

    public function getStats(): array
    {
        $currentDay = getDayCurrent();
        $totalToday = $currentDay ? $currentDay->total : 0;

        $salesData = $this->getSalesData();
        $salesChart = $salesData['sales'];
        $daysLabels = $salesData['days'];

        return [
            Stat::make('Ventas Totales del Día', formatMoney($totalToday))
                ->description('Ingresos generados hoy')
                ->color('success')
                ->icon('heroicon-o-currency-dollar'),
            Stat::make('Últimos 6 Días', formatMoney(array_sum($salesChart) / max(1, count($salesChart))))
                ->description('Promedio de ventas diarias en los últimos 6 días')
                ->chart($salesChart)
                ->color('primary')
                ->icon('heroicon-o-chart-bar'),
            Stat::make('Promedio Diario', formatMoney(array_sum($salesChart) / max(1, count($salesChart))))
                ->description('Promedio de ventas diarias')
                ->color('info')
                ->icon('heroicon-o-chart-bar'),
        ];
    }

    private function getSalesData(): array
    {
        $sales = Day::whereNotNull('finish_day')->orderBy('created_at', 'DESC')->limit(6)->get()->reverse();
        return [
            'days' => $sales->map(fn($s) => date('d-m-Y', strtotime($s->created_at)))->toArray(),
            'sales' => $sales->pluck('total')->toArray(),
        ];
    }
}
