<?php

namespace App\Filament\Widgets;

use App\Models\Day;
use Filament\Forms\Components\DatePicker;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Actions\Action;
use App\Filament\Pages\CashRegister;

class HistoryDayOverview extends BaseWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(Day::query())
            ->heading('Recaudos diarios')
            ->columns([
                TextColumn::make('created_at')->label('Inicio')
                    ->dateTime('d M Y H:i A'),
                TextColumn::make('total')->label('Recaudado')
                    ->sortable()->wrap()
                    ->formatStateUsing(fn($state) => formatMoney($state)),
                TextColumn::make('profit')->label('Ganancias')
                    ->sortable()->wrap()
                    ->formatStateUsing(fn($state) => formatMoney($state)),
                TextColumn::make('finish_day')->label('Cierre')
                    ->dateTime('d M Y H:i A')
            ])
            ->recordActions([
                Action::make('view')->tooltip('Ver')
                    ->hiddenLabel()->icon('heroicon-o-eye')
                    ->url(fn(Day $record) => CashRegister::getUrl(['day' => $record->id]))
            ])
            ->filters([
                Filter::make('created_at')
                    ->schema([
                        DatePicker::make('created_from')->label('Desde'),
                        DatePicker::make('created_until')->label('Hasta'),
                    ])->columns(2)->columnSpan('full')
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn($query, $date) => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn($query, $date) => $query->whereDate('created_at', '<=', $date),
                            );
                    })
            ], FiltersLayout::AboveContent);
    }
}
