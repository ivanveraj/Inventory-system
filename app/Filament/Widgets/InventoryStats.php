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
        $isSuperAdmin = $user && $user->hasRole('SuperAdmin');

        // Calcular estadísticas básicas
        $totalProducts = Product::count();

        // Productos con alerta de stock
        $productsWithStockAlert = Product::where('has_stock_alert', true)
            ->whereColumn('amount', '<', 'min_stock_alert')->count();

        // Calcular valor total del inventario (solo para Super Admin)
        $totalInventoryValue = 0;
        $totalInventoryCost = 0;
        if ($isSuperAdmin) {
            $products = Product::where('is_activated', true)->get();
            foreach ($products as $product) {
                $totalInventoryValue += $product->saleprice * $product->amount;
                $totalInventoryCost += $product->buyprice * $product->amount;
            }
        }

        $stats = [
            Stat::make('Total de Productos', $totalProducts)
                ->description('Productos en el sistema')
                ->color('primary')->icon('heroicon-o-cube'),
            Stat::make('Alertas de Stock', $productsWithStockAlert)
                ->description('Productos bajo stock mínimo')
                ->color('danger')
                ->icon('heroicon-o-exclamation-triangle'),
        ];

        // Agregar estadísticas financieras solo para Super Admin
        if ($isSuperAdmin) {
            $stats[] = Stat::make('Costo Total Inventario', formatMoney($totalInventoryCost))
                ->description('Valor total del precio de compra')
                ->color('warning')->icon('heroicon-o-banknotes');
        }

        return $stats;
    }
}
