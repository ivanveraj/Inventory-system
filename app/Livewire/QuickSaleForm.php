<?php

namespace App\Livewire;

use App\Enums\PaymentMethods;
use App\Http\Traits\NotificationTrait;
use App\Http\Traits\ProductTrait;
use App\Traits\SaleTrait;
use App\Models\Extra;
use App\Models\Product;
use App\Models\SaleTable;
use Filament\Infolists\Components\TextEntry;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Schema;
use Livewire\Component;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Components\Utilities\Get;

class QuickSaleForm extends Component implements HasActions, HasSchemas
{
    use InteractsWithSchemas, InteractsWithActions;
    use SaleTrait, NotificationTrait, ProductTrait;

    public ?array $data = [];
    public ?int $saleId = null;

    public function mount(): void
    {
        $this->syncSale();
    }

    public function syncSale()
    {
        $sale = SaleTable::with('extras')->where('type', 2)->where('state', 0)->latest()->first();
        if (!$sale) {
            $sale = $this->createSaleTable(null, null, 0, 2, null);
        }

        $this->saleId = $sale->id;
        $this->data = [
            'client' => $sale->client,
            'payment_method' => 'efectivo',
            'products' => $sale->extras->map(fn(Extra $extra) => [
                'product_id' => $extra->product_id,
                'amount' => $extra->amount,
                'price' => $extra->price,
                'total' => $extra->total,
            ])->values()->toArray(),
            'total_sale' => $sale->extras->sum('total'),
        ];

        $this->form->fill($this->data);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('client')->label('Cliente')
                    ->live()->placeholder('Nombre del cliente (opcional)')
                    ->afterStateUpdated(function (?string $state): void {
                        if (!$this->saleId) {
                            return;
                        }

                        $sale = SaleTable::find($this->saleId);
                        if ($sale) {
                            $sale->client = $state;
                            $sale->save();
                        }
                    }),
                Select::make('payment_method')->label('Método de Pago')
                    ->options(PaymentMethods::class)
                    ->selectablePlaceholder(false)->required(),
                Repeater::make('products')->label('Productos')
                    ->compact()->columns(4)->deletable()->reorderable(false)
                    ->table([
                        TableColumn::make('Producto')->width('50%'),
                        TableColumn::make('Cantidad')->width('15%'),
                        TableColumn::make('$ Unit')->width('15%'),
                        TableColumn::make('Total')->width('20%'),
                    ])
                    ->schema([
                        Select::make('product_id')->label('Producto')
                            ->placeholder('Seleccione un producto')
                            ->required()->searchable()->reactive()
                            ->afterStateUpdated(function ($state, $set, $get) {
                                if (!$state) {
                                    return;
                                }

                                $product = $this->getProductActive($state);
                                if (!$product) {
                                    $this->customNotification('danger', 'Error', 'El producto no existe o está inactivo.');
                                    $set('product_id', null);
                                    return;
                                }

                                $amount = (int) ($get('amount') ?? 1);
                                if (!$this->syncExtraAmount($product, $amount, 0)) {
                                    $set('product_id', null);
                                    return;
                                }

                                $set('price', $product->saleprice);
                                $set('total', $product->saleprice * $amount);
                                $this->calculateTotal();
                            })
                            ->options($this->getProductOptions())
                            ->getSearchResultsUsing(fn(string $search) => $this->getProductOptionsSearch($search)),
                        TextInput::make('amount')->label('Cantidad')
                            ->numeric()->required()->default(1)->minValue(1)->live()
                            ->afterStateUpdated(function ($state, $old, $set, $get) {
                                if (!$productId = $get('product_id')) {
                                    $this->customNotification('danger', 'Error', "Debe seleccionar un producto.");
                                    return;
                                }

                                $product = $this->getProductActive($productId);
                                if (!$product) {
                                    $this->customNotification('danger', 'Error', "El producto no existe o está inactivo.");
                                    $set('amount', $old ?? 1);
                                    return;
                                }

                                if (!$this->syncExtraAmount($product, (int) $state, (int) ($old ?? 1))) {
                                    $set('amount', $old ?? 1);
                                    return;
                                }

                                $set('total', $product->saleprice * ($state ?? 1));
                                $this->calculateTotal();
                            }),
                        TextEntry::make('price_display')->label('Precio Unit.')
                            ->live()->alignCenter()
                            ->state(fn(Get $get) => formatMoney($get('price') ?? 0)),
                        TextEntry::make('total_display')->label('Total')
                            ->live()->alignCenter()
                            ->state(fn(Get $get) => formatMoney($get('total') ?? 0)),
                        TextInput::make('price')->hidden()->dehydrated(true),
                        TextInput::make('total')->hidden()->dehydrated(true),
                    ])
                    ->addAction(function ($action) {
                        return $action->button()->hiddenLabel()->tooltip('Agregar Producto')
                            ->icon('heroicon-o-plus')->size('md');
                    })
                    ->deleteAction(function ($action) {
                        return $action->before(function (array $arguments, Repeater $component): void {
                            $items = $component->getRawState();
                            $item = $items[$arguments['item']] ?? null;
                            if (!$item) {
                                return;
                            }

                            $productId = $item['product_id'] ?? null;
                            $amount = (int) ($item['amount'] ?? 0);

                            if (!$this->saleId || !$productId || $amount <= 0) {
                                return;
                            }

                            $product = $this->getProductActive($productId);
                            if ($product) {
                                $this->addAmount($product, $amount);
                            }

                            $extra = Extra::where('sale_id', $this->saleId)->where('product_id', $productId)->first();
                            if ($extra) {
                                $extra->amount -= $amount;
                                if ($extra->amount <= 0) {
                                    $extra->delete();
                                } else {
                                    $extra->total = $extra->price * $extra->amount;
                                    $extra->save();
                                }
                            }

                            $this->calculateTotal();
                        });
                    }),
                // TextEntry::make('total_sale')->hiddenLabel()
                //     ->live()->extraAttributes(['class' => 'text-right text-xl font-bold'])
                //     ->state(fn(Get $get) => 'Total a pagar: ' . formatMoney($get('total_sale') ?? 0)),
            ])
            ->statePath('data');
    }

    protected function calculateTotal(): void
    {
        if ($this->saleId) {
            $this->data['total_sale'] = Extra::where('sale_id', $this->saleId)->sum('total');
            return;
        }

        $products = $this->form->getState()['products'] ?? [];
        $this->data['total_sale'] = collect($products)->sum(function (array $item): float {
            $productId = $item['product_id'] ?? null;
            $amount = (int) ($item['amount'] ?? 0);

            if (!$productId || $amount <= 0) {
                return 0;
            }

            $product = $this->getProductActive($productId);

            return $product ? ($product->saleprice * $amount) : 0;
        });
    }


    protected function syncExtraAmount(Product $product, int $amount, ?int $previousAmount = null): bool
    {
        $extra = Extra::where('sale_id', $this->saleId)->where('product_id', $product->id)->first();
        $currentAmount = $previousAmount ?? ($extra?->amount ?? 0);
        $diff = $amount - $currentAmount;

        if ($diff > 0 && $product->amount < $diff) {
            $this->customNotification('danger', 'Error', "Stock insuficiente: {$product->amount} disponible.");
            return false;
        }

        if ($diff > 0) {
            $this->discount($product, $diff);
        } elseif ($diff < 0) {
            $this->addAmount($product, abs($diff));
        }

        $extra ??= new Extra([
            'sale_id' => $this->saleId,
            'product_id' => $product->id,
        ]);

        $extra->name = $product->name;
        $extra->price = $product->saleprice;
        $extra->amount = $amount;
        $extra->total = $product->saleprice * $amount;
        $extra->save();

        return true;
    }

    public function processSale()
    {
        $formData = $this->form->getState();
        $products = collect($formData['products'] ?? []);
        if ($products->isEmpty()) {
            $this->customNotification('danger', 'Error al procesar la venta', 'Debe agregar al menos un producto.');
            return;
        }

        $productIds = $products->pluck('product_id')->unique()->values();
        $productsById = Product::whereIn('id', $productIds)->where('is_activated', 1)->get()->keyBy('id');
        $itemsForInvoice = [];
        foreach ($products as $productData) {
            $product = $productsById->get($productData['product_id']);
            if (!$product) {
                $this->customNotification('danger', 'Error al procesar la venta', "El producto seleccionado no existe o está inactivo.");
                return;
            }

            if ($product->amount < $productData['amount']) {
                $this->customNotification('danger', 'Error al procesar la venta', "El producto {$product->name} no tiene suficiente stock.");
                return;
            }

            $itemsForInvoice[] = [
                'name' => $product->name,
                'amount' => $productData['amount'],
                'total' => $product->saleprice * $productData['amount'],
            ];
        }

        $sale = SaleTable::findOrFail($this->saleId);
        $sale->update([
            'client' => $formData['client'],
            'payment_method' => $formData['payment_method']->value,
            'state' => 1,
        ]);

        $receiptData = $this->endSale($sale);
        
        // Agregar/sobreescribir campos específicos de venta rápida
        $receiptData['payment_method'] = $formData['payment_method']->getLabel();
        $receiptData['sale_random_id'] = now()->format('Ymd') . '-' . str_pad($receiptData['sale_id'], 5, '0', STR_PAD_LEFT);

        session(['receipt_data' => $receiptData]);
        $this->dispatch('printReceipt', url: route('receipt.print'));

        $this->syncSale();
    }

    public function render()
    {
        return view('livewire.quick-sale-form');
    }
}
