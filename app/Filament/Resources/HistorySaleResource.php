<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HistorySaleResource\Pages;
use App\Filament\Resources\HistorySaleResource\RelationManagers;
use App\Models\HistorySale;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HistorySaleResource extends Resource
{
    protected static ?string $model = HistorySale::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $label = 'Historial de ventas';

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('updated_at', 'desc')
            ->columns([
                TextColumn::make('client')
                    ->label('Cliente')
                    ->searchable()->alignCenter(),
                TextColumn::make('time')->label('Tiempo')
                    ->sortable()->alignCenter()
                    ->formatStateUsing(fn($state) => !$state ? '-' : $state . ' min'),
                TextColumn::make('price_time')->label('$ Tiempo')
                    ->sortable()->alignCenter()
                    ->formatStateUsing(fn($state) => !$state ? '-' : formatMoney($state)),
                TextColumn::make('user.name')->label('Cajero')
                    ->sortable()->alignCenter(),
                TextColumn::make('total')
                    ->sortable()->alignCenter()
                    ->formatStateUsing(fn($state) => formatMoney($state)),
                TextColumn::make('updated_at')->label('Finalizado')
                    ->dateTime()->sortable()->alignCenter(),
            ])
            ->actions([
                Action::make('show')
                    ->label('Ver Detalle')
                    ->tooltip('Ver detalles de la venta')
                    ->icon('heroicon-o-eye')
                    ->color('primary')
                    ->size('xl')
                    ->slideOver()
                    ->modalHeading('Detalle de venta')
                    ->modalSubmitActionLabel('Cerrar')
                    ->modalContent(function ($record) {
                        return view('filament.pages.sales.history-detail', [
                            'sale' => $record,
                            'extras' => $record->products,
                            'time' => $record->time,
                            'total' => $record->total,
                            'priceTime' => $record->price_time,
                        ]);
                    }),
            ])
        ;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageHistorySales::route('/'),
        ];
    }
}
