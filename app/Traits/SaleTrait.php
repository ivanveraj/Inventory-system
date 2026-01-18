<?php

namespace App\Traits;

use App\Models\CashMovement;
use App\Models\Product;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
trait SaleTrait
{
    public function addCashMovement($day_id, $type, $concept, $amount, $payment_method, $user_id)
    {
        return CashMovement::create([
            'day_id' => $day_id,
            'type' => $type,
            'concept' => $concept,
            'amount' => $amount,
            'payment_method' => $payment_method,
            'user_id' => $user_id,
        ]);
    }

    public function getProductOptions()
    {
        return Product::where('is_activated', 1)->where('amount', '>', 0)->get()->mapWithKeys(function ($product) {
            return [
                $product->id => "{$product->sku} - {$product->name} (" . $product->amount . "U)",
            ];
        });
    }

    public function getProductOptionsSearch($search)
    {
        return Product::query()->where('name', 'like', "%{$search}%")->orWhere('sku', 'like', "%{$search}%")
            ->get()->mapWithKeys(fn($product) => [
                $product->id => "{$product->sku} - {$product->name} (" . $product->amount . "U)",
            ]);
    }

    public function addProductForm()
    {
        return [
            Grid::make(3)->schema([
                Select::make('product_id')->label('Producto')
                    ->placeholder('Seleccione un producto')
                    ->columnSpan(2)->required()->searchable()
                    ->options($this->getProductOptions())
                    ->getSearchResultsUsing(fn(string $search) => $this->getProductOptionsSearch($search)),
                TextInput::make('amount')->label('Cantidad')
                    ->numeric()->default(1)->minValue(1)->required(),
            ])
        ];
    }
}
