<?php

namespace App\Livewire;

use Livewire\Component;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Tables\Columns\TextColumn;
use App\Models\HistorySale;
use App\Enums\ExpenseType;
use Livewire\Attributes\On;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;
use Filament\Schemas\Components\Grid;

class HistorySales extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithTable, InteractsWithActions, InteractsWithSchemas;

    public $currentDay;

    public function table(Table $table): Table
    {
        return $table->heading('Historial de ventas')
            ->query(HistorySale::query()->where('day_id', $this->currentDay))
            ->defaultSort('created_at', 'desc')
            ->paginated(false)
            ->columns([
                TextColumn::make('user.name')->label('Vendedor')
                    ->sortable()->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('client')->label('Cliente')
                    ->sortable()->searchable(),
                TextColumn::make('time')->label('Tiempo')
                    ->formatStateUsing(fn($state) => $state . ' min')
                    ->toggleable()->sortable()->alignCenter(),
                TextColumn::make('price_time')->label('$ Tiempo')
                    ->formatStateUsing(fn($state) => formatMoney($state))
                    ->toggleable()->sortable()->alignCenter(),
                TextColumn::make('total')->label('Total')
                    ->formatStateUsing(fn($state) => formatMoney($state))
                    ->toggleable()->sortable()->alignCenter(),
                TextColumn::make('created_at')->label('Finalizado')
                    ->dateTime('H:i A')->toggleable()->sortable(),
            ])
            ->recordActions([
                ViewAction::make('show')->tooltip('Ver Detalle')
                    ->hiddenLabel()->icon('heroicon-o-eye')->slideOver()
                    ->modalHeading(fn($record) => "Detalle de venta #{$record->id}")
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('client')->label('Cliente')
                                ->formatStateUsing(fn($state) => $state ?? 'Venta General'),
                            TextEntry::make('created_at')->label('Venta Finalizada')
                                ->formatStateUsing(fn($state) => $state->format('d M Y H:i:s')),
                            TextEntry::make('time')->label('Tiempo')
                                ->formatStateUsing(fn($state) => !$state ? '-' : $state . ' min'),
                            TextEntry::make('price_time')->label('Precio tiempo')
                                ->formatStateUsing(fn($state) => !$state ? '-' : formatMoney($state)),
                            RepeatableEntry::make('products')->label('Productos')
                                ->columnSpanFull()
                                ->table([
                                    TableColumn::make('Nombre'),
                                    TableColumn::make('Cantidad')->alignCenter(),
                                    TableColumn::make('Precio')->alignCenter(),
                                    TableColumn::make('Total')->alignCenter(),
                                ])
                                ->schema([
                                    TextEntry::make('product.name')->label('Producto'),
                                    TextEntry::make('amount')->label('Cantidad')
                                        ->alignCenter(),
                                    TextEntry::make('price')->label('Total')
                                        ->alignCenter()
                                        ->formatStateUsing(fn($state) => formatMoney($state)),
                                    TextEntry::make('id')->label('Total')
                                        ->alignCenter()
                                        ->formatStateUsing(fn($record) => formatMoney($record->amount * $record->price)),
                                ]),
                            TextEntry::make('total')->label('Total')
                                ->size('lg')->weight('bold')->columnSpanFull()
                                ->formatStateUsing(fn($state) => formatMoney($state)),
                        ]),
                    ])
            ])
            ->emptyStateHeading('No hay movimientos registrados')
            ->emptyStateDescription('Agrega ingresos o gastos para este día.');
    }

    public function render()
    {
        return view('livewire.history-sales');
    }
}
