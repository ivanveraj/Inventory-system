<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Dashboard;
use App\Http\Traits\GeneralTrait;
use App\Traits\NotificationTrait;
use App\Http\Traits\SaleTrait;
use App\Http\Traits\SettingTrait;
use App\Http\Traits\TableTrait;
use App\Models\SaleTable;
use App\Tables\Columns\ProductsColumn;
use App\Enums\TableType;
use Carbon\Carbon;
use Filament\Actions\Action as ActionsAction;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Attributes\On;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Forms\Components\TextInput;
use App\Filament\Pages\CashRegister;

class Sales extends Page implements HasTable, HasForms, HasActions
{
    use InteractsWithTable, InteractsWithForms, InteractsWithActions;
    use SaleTrait, NotificationTrait, SettingTrait, GeneralTrait, TableTrait;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-s-currency-dollar';
    protected string $view = 'filament.pages.sales';
    protected static ?string $title = 'Ventas';

    public $day, $minPrice, $minTime, $priceXHour;

    public function mount()
    {
        $this->day = getExistDay();
        if (!$this->day) {
            return redirect()->to(CashRegister::getUrl());
        }
        $settings = $this->getSettings();
        $this->minPrice = $settings['PrecioMinimo'];
        $this->minTime = $settings['TiempoMinimo'];
        $this->priceXHour = $this->getPrecioActual();
    }

    protected function getHeaderActions(): array
    {
        return [
            ActionsAction::make('endDay')->label('Finalizar Día')
                ->icon('heroicon-o-clock')->color('danger')
                ->requiresConfirmation()
                ->hidden(fn() => !$this->day)
                ->modalHeading('Confirmación de Cierre')
                ->modalDescription('Está a punto de finalizar el día. Asegúrese de que todas las ventas estén cerradas antes de continuar. Este proceso no se puede deshacer.')
                ->modalSubmitActionLabel('Sí, finalizar día')
                ->modalCancelActionLabel('Cancelar')
                ->schema([
                    TextInput::make('cash_left_for_next_day')->label('Dinero Base para el día siguiente')
                        ->required()->numeric()->prefix('$')
                        ->hintIcon(...hint_info_tooltip('Este es el dinero base para el día siguiente. Se usará para calcular el saldo de la caja al finalizar el día.'))
                ])
                ->action(function (array $data) {
                    $sales = SaleTable::where('type', 1)->get();
                    foreach ($sales as $sale) {
                        if (!is_null($sale->start_time)) {
                            return self::customNotification('error', 'Error', 'Debe cerrar todas las ventas de las mesas para proceder.');
                        }
                    }

                    // Verificar si hay ventas generales (type 2) abiertas
                    $couldGeneral = SaleTable::where('type', 2)->first();
                    if (!is_null($couldGeneral)) {
                        return self::customNotification('error', 'Error', 'Debe cerrar todas las ventas generales para proceder.');
                    }

                    // Finalizar el día
                    $day = getDay();
                    $day->cash_left_for_next_day = $data['cash_left_for_next_day'];
                    $day->status = 'closed';
                    $day->finish_day = now();
                    $day->save();

                    // Notificación de éxito
                    self::customNotification('success', 'Éxito', 'El día ha sido finalizado correctamente.');
                    return redirect()->to(Dashboard::getUrl());
                })
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->day ? SaleTable::query()->whereHas('table', function ($query) {
                $query->where('state', 1);
            })->where('type', 1) : SaleTable::query()->whereRaw('1 = 0'))
            ->poll(null)->paginated(false)
            ->columns([
                TextColumn::make('table.name')->label('Mesa')
                    ->sortable()->wrap()->searchable(),
                TextColumn::make('start_time')->label('Tiempo transcurrido')
                    ->sortable()->wrap()->alignCenter()->default('-')
                    ->formatStateUsing(function ($record) {
                        if (!$record->start_time || !$record->table?->usesTime()) {
                            return '-';
                        }

                        $diffInSeconds = now()->diffInSeconds(Carbon::parse($record->start_time));
                        $hours = str_pad(floor($diffInSeconds / 3600), 2, "0", STR_PAD_LEFT);
                        $minutes = str_pad(floor(($diffInSeconds % 3600) / 60), 2, "0", STR_PAD_LEFT);
                        $seconds = str_pad($diffInSeconds % 60, 2, "0", STR_PAD_LEFT);
                        return "{$hours}:{$minutes}:{$seconds}";
                    })
                    ->extraAttributes(function ($record) {
                        if (!$record->table?->usesTime()) {
                            return [
                                'class' => 'timer-cell',
                                'data-id' => $record->id,
                                'data-start-time' => '-',
                            ];
                        }

                        return [
                            'class' => 'timer-cell',
                            'data-id' => $record->id,
                            'data-start-time' => $record->start_time ? strtotime($record->start_time) * 1000 : '-',
                        ];
                    }),
                ProductsColumn::make('extras')->label('Extras')
                    ->width('50%'),
                TextColumn::make('id')->label('Total')
                    ->alignCenter()->sortable()
                    ->formatStateUsing(fn($record) => $this->calculateTotal($record, $this->minPrice, $this->minTime, $this->priceXHour)),
            ])
            ->filters([
                SelectFilter::make('table_type')
                    ->label('Tipo de mesa')
                    ->options([
                        TableType::WITH_TIME->value => TableType::WITH_TIME->getLabel(),
                        TableType::WITHOUT_TIME->value => TableType::WITHOUT_TIME->getLabel(),
                    ])
                    ->query(function ($query, array $data) {
                        if (empty($data['value'])) {
                            return $query;
                        }

                        return $query->whereHas('table', fn($tableQuery) => $tableQuery->where('type', $data['value']));
                    }),
            ])
            ->recordActions([
                ActionsAction::make('startTime')->tooltip('Iniciar tiempo')
                    ->hiddenLabel()
                    ->icon('heroicon-o-play')->color('success')->size('lg')
                    ->hidden(fn($record) => !is_null($record->start_time) || !$record->table?->usesTime())
                    ->action(function ($record) {
                        $sale = $this->getSale($record->id);
                        if (is_null($sale)) {
                            return self::customNotification('error', 'Error', 'No existe ninguna venta con este identificador.');
                        }

                        if ($sale->type != 1) {
                            return self::customNotification('error', 'Error', 'La venta no es del tipo correcto.');
                        }

                        if (!$sale->table?->usesTime()) {
                            return self::customNotification('error', 'Error', 'Esta mesa no requiere tiempo.');
                        }

                        $sale->start_time = now();
                        $sale->save();

                        $this->dispatch('startTimer', id: $sale->id, startTime: strtotime($sale->start_time) * 1000);

                        return self::customNotification('success', 'Éxito', 'El tiempo se inició correctamente.');
                    }),
                ActionsAction::make('cancelTime')->tooltip('Cancelar tiempo')
                    ->hiddenLabel()->icon('heroicon-o-x-mark')->color('warning')->size('lg')
                    ->hidden(function ($record) {
                        if (!$record->table?->usesTime() || is_null($record->start_time)) {
                            return true;
                        }

                        $minutes = DateDifference(now(), $record->start_time);
                        return $minutes > $this->minTime;
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Cancelar tiempo')
                    ->modalDescription('Se eliminará el tiempo iniciado y no se registrará ningún pago.')
                    ->modalSubmitActionLabel('Cancelar tiempo')
                    ->modalCancelActionLabel('Cerrar')
                    ->action(function ($record) {
                        $minutes = DateDifference(now(), $record->start_time);
                        if ($minutes > $this->minTime) {
                            return self::customNotification('error', 'Error', 'El tiempo mínimo ya se cumplió.');
                        }

                        $record->start_time = null;
                        $record->save();
                        $this->dispatch('stopTimer', id: $record->id);

                        return self::customNotification('success', 'Éxito', 'El tiempo se canceló correctamente.');
                    }),
                ActionsAction::make('endTime')->tooltip('Finalizar tiempo')
                    ->hiddenLabel()->slideOver()
                    ->icon('heroicon-o-stop')->color('danger')->size('lg')
                    ->hidden(fn($record) => is_null($record->start_time) && $record->extras->count() <= 0)
                    ->modalHeading(fn($record) => 'Detalle del pago (' . ($record->table->name ?? '') . ')')
                    ->modalSubmitActionLabel('Pagado')
                    ->modalCancelActionLabel('Cerrar')
                    ->modalContent(function ($record) {
                        $total = 0;
                        $priceTime = 0;
                        $time = "";

                        if (!is_null($record->start_time) && $record->table?->usesTime()) {
                            $time = DateDifference(date('Y-m-d H:i:s'), $record->start_time);
                            if ($time < $this->minTime) {
                                $total = $this->minPrice;
                            } else {
                                $total = round(($this->priceXHour / 60) * $time);
                            }
                            $priceTime = $total;
                        }

                        foreach ($record->Extras as $extra) {
                            $total += $extra->total;
                        }

                        return view('filament.pages.sales.detail', ['sale' => $record, 'extras' => $record->Extras, 'time' => $time, 'total' => $total, 'priceTime' => $priceTime]);
                    })
                    ->modalSubmitActionLabel('Pagado')
                    ->action(function ($record) {
                        $receiptData = $this->endSale($record);
                        
                        $this->dispatch('stopTimer', id: $record->id);
                        $this->dispatch('refreshExtrasColumn');
                        $this->resetTable();

                        // Guardar e imprimir recibo
                        session(['receipt_data' => $receiptData]);
                        $this->dispatch('printReceipt', url: route('receipt.print'));
                    })
            ]);
    }

    #[On('update-table')]
    public function updateTable()
    {
        $this->resetTable();
    }
}
