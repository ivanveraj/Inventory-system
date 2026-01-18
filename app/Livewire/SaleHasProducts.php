<?php

namespace App\Livewire;

use App\Models\Extra;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\CreateAction;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use App\Models\Product;
use App\Http\Traits\NotificationTrait;
use App\Http\Traits\ProductTrait;
use App\Traits\SaleTrait;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextInputColumn;

class SaleHasProducts extends TableWidget
{
    use InteractsWithTable, InteractsWithForms, InteractsWithActions;
    use SaleTrait, NotificationTrait, ProductTrait;

    public $saleId;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn(): Builder => Extra::query()->where('sale_id', $this->saleId))
            ->heading('')->paginated(false)
            ->columns([
                TextColumn::make('product.name')->label('Producto')
                    ->sortable()->wrap()->limit(15)->tooltip(fn($record) => $record->product->name),
                TextInputColumn::make('amount')->label('Cantidad')
                    ->sortable()->type('number')->rules(['required', 'numeric', 'min:1'])
                    ->extraCellAttributes(['style' => 'min-width: 80px; max-width: 100px;'])
                    ->extraAttributes(['class' => 'small-input-amount'])
                    ->afterStateUpdated(function ($record, $state) {
                        $record->update(['total' => $state * $record->price]);
                        $this->dispatch('update-table');
                    }),
                TextColumn::make('price')->label('Precio')
                    ->sortable()->alignCenter()->wrap()
                    ->formatStateUsing(fn($state) => formatMoney($state)),
                TextColumn::make('total')->label('Total')
                    ->sortable()->alignCenter()->wrap()
                    ->formatStateUsing(fn($state) => formatMoney($state)),
            ])
            ->headerActions([
                CreateAction::make('create')->tooltip('Agregar producto')
                    ->hiddenLabel()->outlined()->color('success')
                    ->modalHeading('Agregar producto')
                    ->modalSubmitActionLabel('Agregar')
                    ->schema($this->addProductForm())
                    ->action(function (array $data) {
                        $product = $this->getProductActive($data['product_id']);
                        if (is_null($product)) {
                            return $this->customNotification('error', 'Error', 'El producto no existe.');
                        }

                        if ($product->amount < $data['amount']) {
                            return $this->customNotification('error', 'Error', 'El producto no tiene la cantidad suficiente.');
                        }

                        $amount = round($data['amount']);
                        if ($product->amount < $amount) {
                            return AccionIncorrecta('', 'No existe la cantidad solicitada en el inventario');
                        }

                        $this->discount($product, $amount);
                        $this->addExtra($this->saleId, $product, $amount);

                        $this->dispatch('update-table');
                        $this->dispatch('refreshExtrasColumn');

                        $this->customNotification('success', 'Exito', "Se agregó {$data['amount']} de {$product->name} correctamente.");
                    })
            ])
            ->recordActions([
                Action::make('delete')->hiddenLabel()->tooltip('Eliminar')
                    ->icon('heroicon-o-trash')->color('danger')->size('sm')
                    ->action(function ($record) {
                        $this->addAmount($record->product, $record->amount);
                        $record->delete();

                        $this->dispatch('update-table');
                        $this->customNotification('success', 'Exito', "Se eliminó {$record->product->name} correctamente.");
                    })
            ])
            ->emptyState(view('tables.empty-state'));
    }
}
