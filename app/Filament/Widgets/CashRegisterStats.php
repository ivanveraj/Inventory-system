<?php

namespace App\Filament\Widgets;

use App\Http\Traits\SettingTrait;
use App\Http\Traits\TableTrait;
use App\Models\HistorySale;
use App\Models\HistoryTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CashRegisterStats extends BaseWidget
{
    use SettingTrait, TableTrait;

    protected function getColumns(): int
    {
        return 6;
    }

    protected function getStats(): array
    {
        $dayId = request()->query('day');
        $day = $dayId ? \App\Models\Day::find($dayId) : getDayCurrent();
        if (!$day) {
            return [
                Stat::make('Sin Día Activo', 'Inicia un nuevo día para comenzar')
                    ->description('No hay un día activo actualmente')
                    ->color('gray')
                    ->icon('heroicon-o-calendar'),
            ];
        }

        $totalSales = $day->total ?? 0;
        $totalIncomes = $day->total_incomes ?? 0;
        $totalExpenses = $day->total_expenses ?? 0;
        $netProfit = $day->net_profit ?? 0;

        // Calcular ingresos por tiempo
        $historyTables = HistoryTable::where('day_id', $day->id)->get();
        $totalTimeRevenue = $historyTables->sum('total');
        $totalTimeMinutes = $historyTables->sum('time');

        // Calcular ingresos por productos
        $historySales = HistorySale::query()
            ->where('day_id', $day->id)
            ->orWhere(function ($query) use ($day) {
                $query->whereNull('day_id')
                    ->whereDate('created_at', $day->created_at->format('Y-m-d'));
            })
            ->get();
        $totalProductRevenue = $historySales->sum('total') - $totalTimeRevenue;

        return [
            Stat::make('Ventas Totales', formatMoney($totalSales))
                ->description('Ventas del día')
                ->color('success')->icon('heroicon-o-currency-dollar'),
            Stat::make('Ganancias Netas', formatMoney($netProfit))
                ->description('V+I-G-R=Ganancias')
                ->color('gray')->icon('heroicon-o-chart-bar'),
            Stat::make('Gastos', formatMoney($totalExpenses))
                ->description('Gastos registrados')
                ->color('danger')
                ->icon('heroicon-o-arrow-up-circle'),
            Stat::make('$ Tiempo', formatMoney($totalTimeRevenue))
                ->description(number_format($totalTimeMinutes) . ' minutos')
                ->color('info')
                ->icon('heroicon-o-clock'),
            Stat::make('$ Productos', formatMoney($totalProductRevenue))
                ->description('Ventas de productos')
                ->color('primary')
                ->icon('heroicon-o-shopping-cart'),
            Stat::make('Otros Ingresos', formatMoney($totalIncomes))
                ->description('Ingresos de otros conceptos')
                ->color('info')
                ->icon('heroicon-o-arrow-down-circle'),
        ];
    }
}
