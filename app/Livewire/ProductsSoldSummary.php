<?php

namespace App\Livewire;

use Livewire\Component;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use App\Models\HistorySale;
use Illuminate\Support\Collection;

class ProductsSoldSummary extends Component implements HasTable, HasActions, HasSchemas
{
    use InteractsWithTable, InteractsWithActions, InteractsWithSchemas;

    public ?int $dayId = null;

    protected function getBaseProductsData(): Collection
    {
        if (!$this->dayId) {
            return collect();
        }

        $sales = HistorySale::where('day_id', $this->dayId)
            ->with('products.product')
            ->get();

        return $sales->flatMap(fn($sale) => $sale->products)
            ->groupBy('product_id')
            ->map(function ($items, $productId) {
                $first = $items->first();
                return [
                    'product_id' => $productId,
                    'product_name' => $first->product?->name ?? 'Producto eliminado',
                    'total_quantity' => $items->sum('amount'),
                    'unit_price' => $first->price,
                    'total_amount' => $items->sum(fn($item) => $item->amount * $item->price),
                ];
            });
    }

    public function getSummary(): array
    {
        $data = $this->getBaseProductsData();
        return [
            'total_products' => $data->count(),
            'total_quantity' => $data->sum('total_quantity'),
            'total_amount' => $data->sum('total_amount'),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->records(function (?string $sortColumn, ?string $sortDirection): Collection {
                $data = $this->getBaseProductsData();
                
                if (filled($sortColumn)) {
                    $data = $data->sortBy(
                        $sortColumn,
                        SORT_REGULAR,
                        $sortDirection === 'desc'
                    );
                } else {
                    $data = $data->sortByDesc('total_quantity');
                }
                
                return $data->values();
            })
            ->heading('Productos Vendidos')
            ->paginated(false)
            ->columns([
                TextColumn::make('product_name')->label('Producto')
                    ->sortable(),
                TextColumn::make('total_quantity')->label('Cantidad')
                    ->alignCenter()
                    ->sortable(),
                TextColumn::make('unit_price')->label('Precio Unit.')
                    ->alignCenter()
                    ->sortable()
                    ->formatStateUsing(fn($state) => formatMoney($state)),
                TextColumn::make('total_amount')->label('Total')
                    ->alignCenter()
                    ->sortable()
                    ->formatStateUsing(fn($state) => formatMoney($state)),
            ])
            ->emptyStateHeading('Sin productos vendidos')
            ->emptyStateDescription('No se vendieron productos este día.');
    }

    public function render()
    {
        return view('livewire.products-sold-summary', [
            'summary' => $this->getSummary(),
        ]);
    }
}
