<?php

namespace App\Livewire;

use App\Http\Traits\NotificationTrait;
use App\Http\Traits\ProductTrait;
use App\Http\Traits\SaleTrait;
use App\Models\Extra;
use App\Models\Product;
use App\Models\SaleTable;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Livewire\Attributes\On;
use Livewire\Component;

class ExtraTableColumn extends Component implements HasForms, HasActions
{
    use InteractsWithActions, InteractsWithForms;
    use NotificationTrait, ProductTrait, SaleTrait;

    public $saleId, $sale, $extras;

    public function addExtraAction()
    {
        return Action::make('addExtra')
            ->label('Producto')
            ->icon('heroicon-o-plus')
            ->color('success')
            ->modalHeading('Agregar producto')
            ->modalSubmitActionLabel('Agregar')
            ->hidden($this->saleId == null)
            ->form([
                Grid::make(3)->schema([
                    Select::make('product_id')
                        ->label('Producto')
                        ->placeholder('Seleccione un producto')
                        ->columnSpan(2)->required()->searchable()
                        ->options(
                            Product::all()->mapWithKeys(function ($product) {
                                return [
                                    $product->id => "{$product->sku} - {$product->name} ($" . $product->saleprice . ")",
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
            ->action(function (array $data) {
                $product = $this->getProduct($data['product_id']);
                if (is_null($product)) {
                    return $this->customNotification('error', 'Error', 'El producto no existe.');
                }

                if ($product->amount < $data['amount']) {
                    return $this->customNotification('error', 'Error', 'El producto no tiene la cantidad suficiente.');
                }

                $this->addExtra($this->sale->id, $product, $data['amount']);
                $this->discount($product, $data['amount']);

                $this->dispatch('update-table');

                $this->customNotification('success', 'Exito', "Se agregó {$data['amount']} de {$product->name} correctamente.");
            });
    }

    public function deleteExtraAction()
    {
        return Action::make('deleteExtra')
            ->hiddenLabel()
            ->icon('heroicon-o-trash')
            ->color('danger')
            ->requiresConfirmation()
            ->action(function ($arguments) {
                $extra = $this->getExtraById($arguments['extraId']);

                if (is_null($extra)) {
                    return;
                }

                $product = $extra->Product;
                if (is_null($product)) {
                    return;
                }

                // Restaurar stock o cantidad
                $this->addAmount($product, $extra->amount);

                // Eliminar el extra
                $extra->delete();

                // Notificación de éxito
                $this->customNotification('success', 'Exito', "Se eliminó {$product->name} correctamente.");
            });
    }

    public function updateExtra($extraId, $newAmount)
    {
        $extra = Extra::find($extraId);
        if ($extra) {
            $extra->update([
                'amount' => $newAmount,
                'total' => $newAmount * $extra->price
            ]);

            $this->dispatch('update-table');
        }
    }

    #[On('refreshExtrasColumn')]
    public function refreshExtrasColumn()
    {
        $this->extras = Extra::where('sale_id', $this->saleId)->get();
    }

    #[On('deleteSale')]
    public function deleteSale($id)
    {
        if ($this->saleId === $id) {
            SaleTable::where('id', $this->saleId)->delete();
            $this->saleId = null;
            $this->sale = null;
            $this->extras = null;
        }else{
            $this->extras = Extra::where('sale_id', $this->saleId)->get();
        }
    }

    public function render()
    {
        if (!$this->saleId || !SaleTable::where('id', $this->saleId)->exists()) {
            return  view('livewire.empty');
        }

        $this->sale = SaleTable::find($this->saleId);
        $this->extras = Extra::where('sale_id', $this->saleId)->get();

        return view('livewire.extra-table-column');
    }
}
