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
        return 4;
    }

    protected function getStats(): array
    {
        return [];
        $page = $this->getLivewire();
        
        if (!$page || !method_exists($page, 'getCurrentDay')) {
            return [];
        }

        $day = $page->getCurrentDay();
        
        if (!$day) {
            return [
                Stat::make('Sin Día Activo', 'Inicia un nuevo día para comenzar')
                    ->description('No hay un día activo actualmente')
                    ->color('gray')
                    ->icon('heroicon-o-calendar'),
            ];
        }
        
        $totalSales = $day->total ?? 0;
        $totalProfit = $day->profit ?? 0;
        $totalIncomes = $day->total_incomes ?? 0;
        $totalExpenses = $day->total_expenses ?? 0;
        $netProfit = $day->net_profit ?? 0;
        
        // Calcular ingresos por tiempo
        $historyTables = HistoryTable::where('day_id', $day->id)->get();
        $totalTimeRevenue = $historyTables->sum('total');
        $totalTimeMinutes = $historyTables->sum('time');
        
        // Calcular ingresos por productos
        $historySales = HistorySale::whereDate('created_at', $day->created_at->format('Y-m-d'))->get();
        $totalProductRevenue = $historySales->sum('total') - $totalTimeRevenue;

        return [
            Stat::make('Ventas Totales', formatMoney($totalSales))
                ->description('Total de ventas del día')
                ->color('success')
                ->icon('heroicon-o-currency-dollar'),
            
            Stat::make('Ganancias Netas', formatMoney($netProfit))
                ->description('Ganancias + Ingresos - Gastos')
                ->color('success')
                ->icon('heroicon-o-chart-bar'),
            
            Stat::make('Ingresos Adicionales', formatMoney($totalIncomes))
                ->description('Ingresos registrados manualmente')
                ->color('info')
                ->icon('heroicon-o-arrow-down-circle'),
            
            Stat::make('Gastos del Día', formatMoney($totalExpenses))
                ->description('Gastos registrados')
                ->color('danger')
                ->icon('heroicon-o-arrow-up-circle'),
            
            Stat::make('Ingresos por Tiempo', formatMoney($totalTimeRevenue))
                ->description(number_format($totalTimeMinutes) . ' minutos')
                ->color('info')
                ->icon('heroicon-o-clock'),
            
            Stat::make('Ingresos por Productos', formatMoney($totalProductRevenue))
                ->description('Ventas de productos')
                ->color('primary')
                ->icon('heroicon-o-shopping-cart'),
        ];
    }
}

