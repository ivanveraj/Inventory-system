<?php

namespace App\Filament\Resources\TableResource\Pages;

use App\Filament\Resources\TableResource;
use App\Http\Traits\SaleTrait;
use Filament\Actions;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageTables extends ManageRecords
{
    use SaleTrait;

    protected static string $resource = TableResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Añadir mesa')
                ->icon('heroicon-o-plus')
                ->mutateDataUsing(function (array $data): array {
                    $data['state'] = 1;
                    return $data;
                })
                ->after(function ($record) {
                    $this->createSaleTable($record->id, null, 1, 1, null);
                }),
        ];
    }
}
