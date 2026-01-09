<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class InventoryStats extends BaseWidget
{
    protected function getColumns(): int
    {
        return 3;
    }

    protected function getStats(): array
    {
        $user = Auth::user();
        $isSuperAdmin = $user && $user->hasRole('Super Admin');

        // Calcular estadísticas básicas
        $totalProducts = Product::count();
        $activeProducts = Product::where('is_activated', true)->count();
        $inactiveProducts = Product::where('is_activated', false)->count();
        
        // Productos con alerta de stock
        $productsWithStockAlert = Product::where('has_stock_alert', true)
            ->whereColumn('amount', '<', 'min_stock_alert')
            ->count();
        
        // Productos activos con alerta de stock
        $activeProductsWithStockAlert = Product::where('is_activated', true)
            ->where('has_stock_alert', true)
            ->whereColumn('amount', '<', 'min_stock_alert')
            ->count();

        // Calcular valor total del inventario (solo para Super Admin)
        $totalInventoryValue = 0;
        $totalInventoryCost = 0;
        $totalProfit = 0;
        
        if ($isSuperAdmin) {
            $products = Product::where('is_activated', true)->get();
            foreach ($products as $product) {
                $totalInventoryValue += $product->saleprice * $product->amount;
                $totalInventoryCost += $product->buyprice * $product->amount;
            }
            $totalProfit = $totalInventoryValue - $totalInventoryCost;
        }

        $stats = [
            Stat::make('Total de Productos', $totalProducts)
                ->description('Productos en el sistema')
                ->color('primary')
                ->icon('heroicon-o-cube'),
            
            Stat::make('Alertas de Stock', $productsWithStockAlert)
                ->description('Productos bajo stock mínimo')
                ->color('danger')
                ->icon('heroicon-o-exclamation-triangle'),
        ];

        // Agregar estadísticas financieras solo para Super Admin
        if ($isSuperAdmin) {
            $stats[] = Stat::make('Valor Total Inventario', formatMoney($totalInventoryValue))
                ->description('Valor a precio de venta')
                ->color('success')
                ->icon('heroicon-o-currency-dollar');
            
            $stats[] = Stat::make('Costo Total Inventario', formatMoney($totalInventoryCost))
                ->description('Costo de compra total')
                ->color('warning')
                ->icon('heroicon-o-banknotes');
            
            $stats[] = Stat::make('Ganancia Potencial', formatMoney($totalProfit))
                ->description('Diferencia entre venta y compra')
                ->color('info')
                ->icon('heroicon-o-chart-bar');
        }

        return $stats;
    }
}

