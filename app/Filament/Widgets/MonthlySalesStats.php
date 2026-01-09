<?php

namespace App\Filament\Widgets;

use App\Http\Traits\DashboardTrait;
use App\Http\Traits\SettingTrait;
use App\Http\Traits\TableTrait;
use App\Models\Day;
use App\Models\CashMovement;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MonthlySalesStats extends BaseWidget
{
    use DashboardTrait, SettingTrait, TableTrait;

    protected function getColumns(): int
    {
        return 4;
    }

    protected function getStats(): array
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();
        
        // Obtener todos los días del mes actual
        $daysThisMonth = Day::whereBetween('created_at', [$currentMonth, $currentMonthEnd])->get();
        
        // Calcular métricas del mes
        $totalSales = $daysThisMonth->sum('total');
        $totalProfit = $daysThisMonth->sum('profit');
        $totalDays = $daysThisMonth->whereNotNull('finish_day')->count();
        $averageDaily = $totalDays > 0 ? $totalSales / $totalDays : 0;
        
        // Calcular ingresos y gastos del mes
        $monthTransactions = CashMovement::whereHas('day', function($query) use ($currentMonth, $currentMonthEnd) {
            $query->whereBetween('created_at', [$currentMonth, $currentMonthEnd]);
        })->get();
        
        $totalIncomes = $monthTransactions->where('type', 'income')->sum('amount');
        $totalExpenses = $monthTransactions->where('type', 'expense')->sum('amount');
        $netProfit = $totalProfit + $totalIncomes - $totalExpenses;
        
        // Comparar con mes anterior
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();
        $daysLastMonth = Day::whereBetween('created_at', [$lastMonth, $lastMonthEnd])->get();
        $totalSalesLastMonth = $daysLastMonth->sum('total');
        $salesChange = $totalSalesLastMonth > 0 
            ? (($totalSales - $totalSalesLastMonth) / $totalSalesLastMonth) * 100 
            : 0;

        return [
            Stat::make('Ventas del Mes', formatMoney($totalSales))
                ->description($salesChange >= 0 ? "↑ " . number_format(abs($salesChange), 1) . "% vs mes anterior" : "↓ " . number_format(abs($salesChange), 1) . "% vs mes anterior")
                ->descriptionIcon($salesChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($salesChange >= 0 ? 'success' : 'danger')
                ->icon('heroicon-o-currency-dollar'),
            
            Stat::make('Ganancias Netas', formatMoney($netProfit))
                ->description('Ganancias + Ingresos - Gastos')
                ->color('success')
                ->icon('heroicon-o-chart-bar'),
            
            Stat::make('Ingresos Adicionales', formatMoney($totalIncomes))
                ->description('Ingresos registrados manualmente')
                ->color('info')
                ->icon('heroicon-o-arrow-down-circle'),
            
            Stat::make('Gastos del Mes', formatMoney($totalExpenses))
                ->description('Gastos registrados')
                ->color('danger')
                ->icon('heroicon-o-arrow-up-circle'),
            
            Stat::make('Promedio Diario', formatMoney($averageDaily))
                ->description('Promedio de ventas por día')
                ->color('primary')
                ->icon('heroicon-o-calculator'),
            
            Stat::make('Días Activos', $totalDays)
                ->description('Días con ventas completadas')
                ->color('gray')
                ->icon('heroicon-o-calendar-days'),
        ];
    }
}

