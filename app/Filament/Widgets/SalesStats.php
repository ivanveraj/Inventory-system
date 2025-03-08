<?php

namespace App\Filament\Widgets;

use App\Http\Traits\DashboardTrait;
use App\Http\Traits\GeneralTrait;
use App\Http\Traits\SettingTrait;
use App\Http\Traits\TableTrait;
use App\Models\Day;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SalesStats extends BaseWidget
{
    use DashboardTrait, SettingTrait, TableTrait;

    public function getStats(): array
    {
        $currentDay = getDayCurrent();
        $totalToday = $currentDay ? $currentDay->total : 0;

        $salesData = $this->getSalesData();
        $salesChart = $salesData['sales'];
        $daysLabels = $salesData['days'];

        $currentSales = $currentDay ? $currentDay->total : 0;
        $currentProfit = $currentDay ? $currentDay->profit : 0;
        $currentTValueTable = $this->getTotalTimeRevenue($currentDay);
        $revenueFromProducts = $currentSales - $currentTValueTable;

        return [
            Stat::make('Total en caja', formatMoney($currentSales))
                ->description('Ingresos generados hoy')
                ->color('danger')
                ->icon('heroicon-o-currency-dollar'),
            Stat::make('Recaudado X Productos', formatMoney($revenueFromProducts))
                ->description('Ingresos por productos vendidos')
                ->color('success')
                ->icon('heroicon-o-shopping-cart'),
            Stat::make('Recaudado X Tiempo', formatMoney($currentTValueTable))
                ->description('Ingresos por tiempo de uso')
                ->color('info')
                ->icon('heroicon-o-clock'),
            Stat::make('Ganancias del día', formatMoney($currentProfit))
                ->description('Ganancias netas del día')
                ->color('secondary')
                ->icon('heroicon-o-chart-bar'),
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

    private function getTotalTimeRevenue($day)
    {
        if (!$day) {
            return 0;
        }

        $priceTime = $this->getSetting('PrecioXHora');
        $historyTables = $this->getHistoryTables($day->id);
        $totalTimeRevenue = 0;

        foreach ($historyTables as $historyT) {
            $totalTimeRevenue += round(($priceTime / 60) * $historyT->time);
        }

        return $totalTimeRevenue;
    }
}
