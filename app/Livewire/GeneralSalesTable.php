<?php

namespace App\Livewire;

use Filament\Actions\DeleteAction;
use Filament\Schemas\Components\Grid;
use App\Http\Traits\NotificationTrait;
use App\Http\Traits\ProductTrait;
use App\Http\Traits\SaleTrait;
use App\Models\SaleTable;
use App\Models\Product;
use App\Models\Extra;
use App\Tables\Columns\ExtraTableColumn;
use Filament\Actions\Action as ActionsAction;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Attributes\On;
use Livewire\Component;

class GeneralSalesTable extends Component implements HasForms, HasTable, HasActions
{
    use InteractsWithTable, InteractsWithForms, InteractsWithActions;
    use SaleTrait, NotificationTrait,ProductTrait;

    public function table(Table $table): Table
    {
        return $table
            ->query(SaleTable::query()->where('type', 2))
            ->headerActions([
                ActionsAction::make('create')->label('Nueva venta')
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
                    ->extraCellAttributes(['class' => 'w-1/2'])
                    ->extraHeaderAttributes(['class' => 'w-1/2']),

                TextColumn::make('id')
                    ->label('Total')
                    ->alignCenter()
                    ->formatStateUsing(fn($record) => $this->calculateTotal($record))
                    ->sortable()
                    ->extraCellAttributes(['class' => 'w-1/4']) // 25% del ancho
                    ->extraHeaderAttributes(['class' => 'w-1/4']),
            ])
            ->recordActions([
                ActionsAction::make('show')
                    ->hiddenLabel()
                    ->tooltip('Finalizar venta')
                    ->icon('heroicon-o-eye')
                    ->color('success')
                    ->size('xl')
                    ->hidden(fn($record) => $record->extras->count() <= 0)
                    ->modalHeading('Detalle del pago')
                    ->modalSubmitActionLabel('Pagado')
                    ->modalCancelActionLabel('Cerrar')
                    ->slideOver()
                    ->modalContent(function ($record) {
                        $total = 0;

                        $extras = $record->Extras;
                        foreach ($extras as $ext) {
                            $total += $ext->total;
                        }

                        return view('filament.pages.sales.detail', ['sale' => $record, 'extras' => $record->Extras, 'total' => $total]);
                    })
                    ->modalSubmitActionLabel('Pagado')
                    ->action(function ($record) {
                        $this->endSale($record);
                        $this->resetTable();
                    }),
                DeleteAction::make('delete')
                    ->hiddenLabel()
                    ->tooltip('Eliminar venta')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->size('xl')
                    ->requiresConfirmation(false)
                    ->visible(fn($record) => $this->calculateTotal($record) == '$0')
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

    public function addExtraAction()
    {
        return ActionsAction::make('addExtra')
            ->hiddenLabel()
            ->icon('heroicon-o-plus')
            ->color('success')
            ->outlined()
            ->modalHeading('Agregar producto')
            ->modalSubmitActionLabel('Agregar')
            ->schema([
                Grid::make(3)->schema([
                    Select::make('product_id')
                        ->label('Producto')
                        ->placeholder('Seleccione un producto')
                        ->columnSpan(2)->required()->searchable()
                        ->options(
                            Product::where('is_activated', 1)->get()->mapWithKeys(function ($product) {
                                return [
                                    $product->id => "{$product->sku} - {$product->name} ($" . $product->amount . ")",
                                ];
                            })
                        )
                        ->getSearchResultsUsing(function (string $search) {
                            return Product::query()->where('name', 'like', "%{$search}%")->orWhere('sku', 'like', "%{$search}%")
                                ->get()->mapWithKeys(fn($product) => [
                                    $product->id => "{$product->sku} - {$product->name} ($" . $product->saleprice . ")",
                                ]);
                        }),
                    TextInput::make('amount')->label('Cantidad')
                        ->numeric()->default(1)->minValue(1)->required(),
                ])
            ])
            ->action(function ($arguments, array $data) {
                $saleId = $arguments['saleId'];
                $product = $this->getProductActive($data['product_id']);
                if (is_null($product)) {
                    return $this->customNotification('error', 'Error', 'El producto no existe.');
                }

                if ($product->amount < $data['amount']) {
                    return $this->customNotification('error', 'Error', 'El producto no tiene la cantidad suficiente.');
                }

                $amount = round($data['amount']);
                if ($product->amount < $amount) {
                    return $this->customNotification('error', 'Error', 'No existe la cantidad solicitada en el inventario');
                }

                $this->discount($product, $amount);
                $this->addExtra($saleId, $product, $amount);

                $this->dispatch('update-table');
                $this->customNotification('success', 'Exito', "Se agregó {$data['amount']} de {$product->name} correctamente.");
            });
    }
}
