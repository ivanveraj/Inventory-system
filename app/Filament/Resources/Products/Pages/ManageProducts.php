<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Exports\ProductExporter;
use App\Filament\Widgets\InventoryStats;
use Filament\Actions\Exports\Enums\ExportFormat;

class ManageProducts extends ManageRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make('create_product')->label('Añadir producto')
                ->icon('heroicon-o-plus')->slideOver(),
            ExportAction::make('export')
                ->label('Exportar Inventario')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->exporter(ProductExporter::class)
                ->formats([ExportFormat::Xlsx,]),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            InventoryStats::class,
        ];
    }
}
