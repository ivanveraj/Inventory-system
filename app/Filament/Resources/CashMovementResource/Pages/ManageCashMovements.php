<?php

namespace App\Filament\Resources\CashMovementResource\Pages;

use App\Filament\Resources\CashMovementResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageCashMovements extends ManageRecords
{
    protected static string $resource = CashMovementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Nuevo Movimiento')
                ->icon('heroicon-o-plus')
                ->mutateFormDataUsing(function (array $data): array {
                    if (request()->has('day_id')) {
                        $data['day_id'] = request()->get('day_id');
                    }
                    return $data;
                }),
        ];
    }
}

