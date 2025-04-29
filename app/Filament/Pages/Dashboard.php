<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\HistoryDayOverview;
use App\Filament\Widgets\HistoryTableOverview;
use App\Filament\Widgets\SalesStats;
use Filament\Pages\Page;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.dashboard';

    protected function getHeaderWidgets(): array
    {
        return [
            SalesStats::class,
            HistoryDayOverview::class,
            HistoryTableOverview::class
        ];
    }
}
