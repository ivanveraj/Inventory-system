<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\HistoryDayOverview;
use App\Filament\Widgets\HistoryTableOverview;
use App\Filament\Widgets\MonthlySalesStats;
use App\Filament\Widgets\SalesStats;
use Filament\Pages\Page;

class Dashboard extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-text';
    protected string $view = 'filament.pages.dashboard';

    protected function getHeaderWidgets(): array
    {
        return [
            MonthlySalesStats::class,
            SalesStats::class,
            HistoryDayOverview::class,
            HistoryTableOverview::class
        ];
    }
}
