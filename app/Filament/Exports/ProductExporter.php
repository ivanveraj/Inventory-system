<?php

namespace App\Filament\Exports;

use App\Enums\ProductCategory;
use App\Models\Product;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class ProductExporter extends Exporter
{
    protected static ?string $model = Product::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('name')
                ->label('Nombre del Producto'),
            ExportColumn::make('description')
                ->label('Descripción'),
            ExportColumn::make('category')
                ->label('Categoría')
                ->formatStateUsing(fn($state) => $state ? ProductCategory::getName($state) : ''),
            ExportColumn::make('sku')
                ->label('Código SKU'),
            ExportColumn::make('buyprice')
                ->label('Precio de Compra')
                ->formatStateUsing(fn($state) => $state ? formatMoney($state) : ''),
            ExportColumn::make('saleprice')
                ->label('Precio de Venta')
                ->formatStateUsing(fn($state) => $state ? formatMoney($state) : ''),
            ExportColumn::make('amount')
                ->label('Cantidad en Inventario'),
            ExportColumn::make('is_activated')
                ->label('Estado')
                ->formatStateUsing(fn($state) => $state ? 'Activo' : 'Inactivo'),
            ExportColumn::make('has_stock_alert')
                ->label('Tiene Alerta de Stock')
                ->formatStateUsing(fn($state) => $state ? 'Sí' : 'No'),
            ExportColumn::make('min_stock_alert')
                ->label('Stock Mínimo'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'La exportación de productos se ha completado y se exportaron ' . Number::format($export->successful_rows) . ' ' . str('fila')->plural($export->successful_rows) . '.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('fila')->plural($failedRowsCount) . ' fallaron al exportar.';
        }

        return $body;
    }
}
