<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Dashboard;
use App\Http\Traits\GeneralTrait;
use App\Http\Traits\NotificationTrait;
use App\Http\Traits\SaleTrait;
use App\Http\Traits\SettingTrait;
use App\Http\Traits\TableTrait;
use App\Models\SaleTable;
use App\Tables\Columns\ExtraTableColumn;
use App\Tables\Columns\ProductsColumn;
use Carbon\Carbon;
use Filament\Actions\Action as ActionsAction;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class Sales extends Page implements HasTable, HasForms
{
    use InteractsWithTable, InteractsWithForms;
    use SaleTrait, NotificationTrait, SettingTrait, GeneralTrait, TableTrait;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-s-currency-dollar';
    protected string $view = 'filament.pages.sales';
    protected static ?string $title = 'Ventas';

    public $day, $minPrice, $minTime, $priceXHour;

    public function mount()
    {
        $this->day = getExistDay();
        $this->minPrice = $this->getSetting('PrecioMinimo');
        $this->minTime = $this->getSetting('TiempoMinimo');
        $this->priceXHour = $this->getPrecioActual();
    }

    protected function getHeaderActions(): array
    {
        return [
            ActionsAction::make('endDay')
                ->label('Finalizar Día')
                ->icon('heroicon-o-clock')
                ->color('danger')
                ->requiresConfirmation()
                ->hidden(fn() => $this->day == true ? false : true)
                ->modalHeading('Confirmación de Cierre')
                ->modalDescription('Está a punto de finalizar el día. Asegúrese de que todas las ventas estén cerradas antes de continuar. Este proceso no se puede deshacer.')
                ->modalSubmitActionLabel('Sí, finalizar día')
                ->modalCancelActionLabel('Cancelar')
                ->action(function () {
                    // Obtener todas las ventas de mesas (type 1)
                    $sales = SaleTable::where('type', 1)->get();

                    // Verificar si hay alguna mesa con `start_time` activo
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
            ->query($this->day ? SaleTable::query()->where('type', 1) : SaleTable::query()->whereRaw('1 = 0'))
            ->poll(null)
            ->paginated(false)
            ->columns([
                TextColumn::make('table.name')
                    ->label('Mesa')
                    ->sortable()->wrap()
                    ->searchable(),
                TextColumn::make('start_time')
                    ->label('Tiempo transcurrido')
                    ->sortable()->wrap()->alignCenter()->default('-')
                    ->formatStateUsing(function ($record) {
                        if (!$record->start_time) {
                            return '-';
                        }

                        $diffInSeconds = now()->diffInSeconds(Carbon::parse($record->start_time));
                        $hours = str_pad(floor($diffInSeconds / 3600), 2, "0", STR_PAD_LEFT);
                        $minutes = str_pad(floor(($diffInSeconds % 3600) / 60), 2, "0", STR_PAD_LEFT);
                        $seconds = str_pad($diffInSeconds % 60, 2, "0", STR_PAD_LEFT);
                        return "{$hours}:{$minutes}:{$seconds}";
                    })
                    ->extraAttributes(function ($record) {
                        return [
                            'class' => 'timer-cell',
                            'data-id' => $record->id,
                            'data-start-time' => $record->start_time ? strtotime($record->start_time) * 1000 : '-',
                        ];
                    }),
                ProductsColumn::make('extras')
                    ->label('Extras'),
                TextColumn::make('id')
                    ->label('Total')
                    ->alignCenter()
                    ->formatStateUsing(fn($record) => $this->calculateTotal($record, $this->minPrice, $this->minTime, $this->priceXHour))
                    ->sortable(),
            ])
            ->recordActions([
                ActionsAction::make('startTime')
                    ->tooltip('Iniciar tiempo')
                    ->hiddenLabel()
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->size('xl')
                    ->hidden(fn($record) => !is_null($record->start_time))
                    ->action(function ($record) {
                        $sale = $this->getSale($record->id);
                        if (is_null($sale)) {
                            return self::customNotification('danger', 'Error', 'No existe ninguna venta con este identificador.');
                        }

                        if ($sale->type != 1) {
                            return self::customNotification('danger', 'Error', 'La venta no es del tipo correcto.');
                        }

                        $sale->start_time = now();
                        $sale->save();

                        $this->dispatch('startTimer', id: $sale->id, startTime: strtotime($sale->start_time) * 1000);

                        return self::customNotification('success', 'Éxito', 'El tiempo se inició correctamente.');
                    }),
                ActionsAction::make('endTime')
                    ->hiddenLabel()
                    ->tooltip('Finalizar tiempo')
                    ->icon('heroicon-o-stop')
                    ->color('danger')
                    ->size('xl')
                    ->hidden(fn($record) => is_null($record->start_time) && $record->extras->count() <= 0)
                    ->modalHeading(fn($record) => 'Detalle del pago (' . ($record->table->name ?? '') . ')')
                    ->modalSubmitActionLabel('Pagado')
                    ->modalCancelActionLabel('Cerrar')
                    ->slideOver()
                    ->modalContent(function ($record) {
                        $total = 0;
                        $priceTime = 0;
                        $time = "";

                        if (!is_null($record->start_time)) {
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
                        $this->endSale($record);
                        $this->dispatch('stopTimer', id: $record->id);
                        $this->dispatch('refreshExtrasColumn');
                        $this->resetTable();
                    })
            ])
            ->emptyStateHeading('No hay un día activo')
            ->emptyStateDescription('Para iniciar las ventas, inicia un nuevo día.')
            ->emptyStateActions([
                ActionsAction::make('startDay')
                    ->label('Iniciar Día')
                    ->icon('heroicon-o-calendar')
                    ->color('info')
                    ->action(function () {
                        getDay();
                        $this->day = true;
                        $this->customNotification('success', 'Éxito', 'El día se inició correctamente.');
                        $this->resetTable();
                    }),
            ]);
    }

    #[On('update-table')]
    public function updateTable()
    {
        $this->resetTable();
    }
}
