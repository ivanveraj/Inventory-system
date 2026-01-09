<?php

namespace App\Filament\App\Resources\Suppliers\Pages;

use App\Filament\Resources\Suppliers\SupplierResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageSuppliers extends ManageRecords
{
    protected static string $resource = SupplierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make('create')
                ->label('Añadir proveedor')
                ->icon('heroicon-o-plus')
                ->modalHeading('Crear Proveedor')
                ->mutateDataUsing(function (array $data): array {
                    if (isset($data['schedule']) && is_array($data['schedule'])) {
                        $data['schedule'] = json_encode($data['schedule']);
                    }
                
                    return $data;
                }),
        ];
    }
}
