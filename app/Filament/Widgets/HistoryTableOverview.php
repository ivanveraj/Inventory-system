<?php

namespace App\Filament\Widgets;

use App\Models\HistoryTable;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class HistoryTableOverview extends BaseWidget
{

    public function table(Table $table): Table
    {
        return $table
            ->query(HistoryTable::query())
            ->heading('Historial de recaudos en mesas')
            ->columns([
                TextColumn::make('table.name')->label('Mesa')
                    ->sortable()->wrap()
                    ->searchable(),
                TextColumn::make('time')->label('Tiempo')
                    ->sortable()->wrap()
                    ->formatStateUsing(fn($state) => $state . ' min (' . number_format($state / 60, 2) . ' h)'),
                TextColumn::make('total')->label('$ Recaudado')->sortable()
                    ->formatStateUsing(fn($state) => formatMoney($state)),
                TextColumn::make('Day.created_at')->label('Fecha')
                    ->dateTime('d M Y H:i A')->sortable()
            ])
            ->filters([
                SelectFilter::make('day_id')
                    /* ->label('Filtrar por fecha')
                    ->relationship('Day', 'created_at')->preload()
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->created_at->format('d M Y')) */
                    ->searchable(),
            ],FiltersLayout::AboveContent);
    }
}
