<?php

namespace App\Livewire;

use App\Http\Traits\NotificationTrait;
use App\Http\Traits\SaleTrait;
use App\Livewire\ExtraTableColumn as LivewireExtraTableColumn;
use App\Models\Extra;
use App\Models\SaleTable;
use App\Models\Shop\Product;
use App\Tables\Columns\ExtraTableColumn;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class GeneralSalesTable extends Component implements HasForms, HasTable
{
    use InteractsWithTable, InteractsWithForms;
    use SaleTrait, NotificationTrait;

    public function table(Table $table): Table
    {
        return $table
            ->query(SaleTable::query()->where('type', 2))
            ->headerActions([
                Action::make('create')->label('Nueva venta')
                    ->icon('heroicon-s-plus')->color('primary')
                    ->action(function () {
                        $this->createSaleTable(null, null, 1, 2, null);
                    })
            ])
            ->columns([
                TextInputColumn::make('client')
                    ->label('Cliente')
                    ->placeholder('Nombre del cliente')
                    ->sortable()->searchable()
                    ->extraCellAttributes(['class' => 'w-1/6']) // 20% del ancho
                    ->extraHeaderAttributes(['class' => 'w-1/6']),
                ExtraTableColumn::make('extras')->label('Extras')
                    ->extraCellAttributes(['class' => 'w-1/2']) // 50% del ancho
                    ->extraHeaderAttributes(['class' => 'w-1/2']),
                TextColumn::make('id')
                    ->label('Total')
                    ->alignCenter()
                    ->formatStateUsing(fn($record) => $this->calculateTotal($record))
                    ->sortable()
                    ->extraCellAttributes(['class' => 'w-1/4']) // 25% del ancho
                    ->extraHeaderAttributes(['class' => 'w-1/4']),
            ])
            ->actions([
                Action::make('show')
                    ->hiddenLabel()
                    ->tooltip('Finalizar venta')
                    ->icon('heroicon-o-eye')
                    ->color('success')
                    ->size('xl')
                    ->hidden(fn($record) => $record->extras->count() <= 0)
                    ->modalHeading(fn($record) => 'Detalle del pago')
                    ->modalSubmitActionLabel('Pagado')
                    ->modalCancelActionLabel('Cerrar')
                    ->slideOver()
                    ->modalContent(function ($record) {
                        $total = 0;
                        $priceTime = 0;
                        $time = "";

                        if (!is_null($record->start_time)) {
                            $TiempoMinimo = $this->getSetting('TiempoMinimo');
                            $time = DateDifference(date('Y-m-d H:i:s'), $record->start_time);
                            if ($time < $TiempoMinimo) {
                                $total = $this->getSetting('PrecioMinimo');
                            } else {
                                $PrecioXHora = $this->getSetting('PrecioXHora');
                                $total = round(($PrecioXHora / 60) * $time);
                            }
                            $priceTime = $total;
                        }

                        if ($record->type == 2) {
                            $total = 0;
                        }

                        $extras = $record->Extras;
                        foreach ($extras as $ext) {
                            $total += $ext->total;
                        }
                        return view('filament.pages.sales.detail', ['sale' => $record, 'extras' => $record->Extras, 'time' => $time, 'total' => $total, 'priceTime' => $priceTime]);
                    })
                    ->modalSubmitActionLabel('Pagado')
                    ->action(function ($record) {
                        $total = 0;

                        $extras = $record->Extras;
                        foreach ($extras as $ext) {
                            $total += $ext->total;
                        }

                        $day = getDay();
                        $day->total += $total;
                        $day->save();
                        Extra::where('sale_id', $record->id)->delete();

                        $this->customNotification('success', 'Éxito', 'La venta se finalizó correctamente.');
                        $this->dispatch('deleteSale', id: $record->id);
                        $this->resetTable();
                    })
            ])
        ;
    }

    #[On('update-table')]
    public function updateTable()
    {
        $this->resetTable();
    }

    public function render()
    {
        return view('livewire.general-sales-table');
    }
}
