<?php

namespace App\Livewire;

use Filament\Schemas\Components\Grid;
use App\Http\Traits\NotificationTrait;
use App\Http\Traits\ProductTrait;
use App\Http\Traits\SaleTrait;
use App\Models\Extra;
use App\Models\Product;
use App\Models\SaleTable;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Livewire\Attributes\On;
use Livewire\Component;
use Filament\Actions\Action;
use App\Traits\SaleTrait as TraitsSaleTrait;

class ExtraTableColumn extends Component implements HasForms, HasActions
{
    use InteractsWithActions, InteractsWithForms;
    use NotificationTrait, ProductTrait, SaleTrait, TraitsSaleTrait;

    public $saleId, $sale, $extras;

    public function addExtraAction(): Action
    {
        return Action::make('addExtra')
            ->hiddenLabel()->outlined()
            ->icon('heroicon-o-plus')
            ->color('success')
            ->modalHeading('Agregar producto')
            ->modalSubmitActionLabel('Agregar')
            ->hidden($this->saleId == null)
            ->schema([
                Grid::make(3)->schema([
                    Select::make('product_id')
                        ->label('Producto')
                        ->placeholder('Seleccione un producto')
                        ->columnSpan(2)->required()->searchable()
                        ->options($this->getProductOptions())
                        ->getSearchResultsUsing(fn(string $search) => $this->getProductOptionsSearch($search)),
                    TextInput::make('amount')->label('Cantidad')
                        ->numeric()->default(1)->minValue(1)->required(),
                ])
            ])
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
            });
    }

    public function deleteExtraAction(): Action
    {
        return Action::make('deleteExtra')->hiddenLabel()
            ->icon('heroicon-o-trash')->color('danger')->size('sm')
            ->tooltip('Eliminar')
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
                $this->dispatch('update-table');

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

    public function render()
    {
        $this->sale = SaleTable::find($this->saleId);
        $this->extras = Extra::where('sale_id', $this->saleId)->get();
        return view('livewire.extra-table-column');
    }
}
