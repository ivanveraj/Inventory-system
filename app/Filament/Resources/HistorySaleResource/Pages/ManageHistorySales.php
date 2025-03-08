<?php

namespace App\Filament\Resources\HistorySaleResource\Pages;

use App\Filament\Resources\HistorySaleResource;
use Filament\Resources\Pages\ManageRecords;

class ManageHistorySales extends ManageRecords
{
    protected static string $resource = HistorySaleResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
