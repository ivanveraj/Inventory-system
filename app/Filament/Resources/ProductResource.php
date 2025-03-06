<?php

namespace App\Filament\Resources;

use App\Enums\ProductCategory;
use App\Enums\ProductType;
use App\Filament\Resources\ProductResource\Pages\CreateProduct;
use App\Filament\Resources\ProductResource\Pages\EditProduct;
use App\Filament\Resources\ProductResource\Pages\ListProducts;
use App\Filament\Resources\ProductResource\Pages\ManageProducts;
use App\Models\Product;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-table-cells';
    protected static ?string $label = 'Inventario';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre del Producto')
                            ->placeholder('Ingrese el nombre del producto')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('sku')
                            ->label('Código SKU')
                            ->placeholder('Ingrese el código SKU único')
                            ->required()
                            ->maxLength(255)
                            ->reactive()
                            ->formatStateUsing(fn($state) => strtoupper($state))
                            ->helperText('El SKU se mostrará en mayúsculas automáticamente.'),
                        Select::make('category')
                            ->label('Categoria')
                            ->placeholder('Seleccione la categoria del producto')
                            ->options(ProductCategory::class)
                            ->required(),
                        RichEditor::make('description')
                            ->columnSpanFull(),
                        TextInput::make('buyprice')
                            ->label('Precio de Compra')
                            ->placeholder('Ingrese el precio de compra (ej. 1500)')
                            ->required()
                            ->numeric()
                            ->reactive(),
                        TextInput::make('saleprice')
                            ->label('Precio de Venta')
                            ->placeholder('Ingrese el precio de venta (ej. 2000)')
                            ->required()
                            ->numeric()
                            ->reactive()
                            ->gte('buyprice'),
                        TextInput::make('amount')
                            ->label('Cantidad en Inventario')
                            ->placeholder('Ingrese la cantidad disponible')
                            ->required()
                            ->numeric()
                            ->disabledOn('edit'),
                        Toggle::make('is_activated')
                            ->label('Activado')
                            ->onIcon('heroicon-o-check-circle')
                            ->offIcon('heroicon-o-x-circle')
                            ->onColor('success')
                            ->offColor('danger')
                            ->default(true)
                            ->inline(false),
                        Toggle::make('has_stock_alert')
                            ->label('Activar Alerta de Stock')
                            ->onIcon('heroicon-o-check-circle')
                            ->offIcon('heroicon-o-x-circle')
                            ->onColor('success')
                            ->offColor('danger')
                            ->default(true)
                            ->reactive()
                            ->inline(false),
                        TextInput::make('min_stock_alert')
                            ->label('Alerta de Stock Mínimo')
                            ->placeholder('Ingrese la cantidad mínima para la alerta')
                            ->required()
                            ->numeric()
                            ->reactive()
                            ->hidden(fn($get) => !$get('has_stock_alert')),
                        TextInput::make('barcode')
                            ->label('Código de Barras')
                            ->placeholder('Ingrese el código de barras del producto')
                            ->maxLength(255),
                    ])->columns(3)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Producto')->searchable(),
                TextColumn::make('sku')->label('SKU')->searchable(),
                TextColumn::make('amount')
                    ->label('Cantidad')->alignCenter()->sortable()
                    ->formatStateUsing(function ($record) {
                        return $record->amount;
                    })
                    ->color(function (Product $product) {
                        return $product->has_stock_alert && $product->amount < $product->min_stock_alert ? 'danger' : null;
                    }),
                TextColumn::make('buyprice')
                    ->label('Compra')
                    ->formatStateUsing(function (Product $product) {
                        return '$ ' . number_format($product->buyprice, 0);
                    })
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                TextColumn::make('saleprice')
                    ->label('Venta')
                    ->html()
                    ->formatStateUsing(function (Product $product) {
                        return "$ " . number_format($product->saleprice, 0);
                    })
                    ->sortable(),
                TextColumn::make('is_activated')
                    ->label('Estado')
                    ->badge()
                    ->color(fn(int $state): string => match ($state) {
                        0 => 'danger',
                        1 => 'success',
                        2 => 'warning'
                    })
                    ->formatStateUsing(function (Product $product) {
                        switch ($product->is_activated) {
                            case 0:
                                return 'Inactivo';
                            case 1:
                                return 'Activo';
                        }
                    })
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Categorias')
                    ->multiple()
                    ->options(ProductCategory::class),
                Filter::make('stock_alert')
                    ->label('Productos con Alerta de Stock')
                    ->toggle()
                    ->query(function ($query) {
                        return $query->where('has_stock_alert', true)
                            ->whereColumn('amount', '<', 'min_stock_alert');
                    }),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormWidth('full')
            ->defaultGroup('category')
            ->groups([
                Group::make('category')->label('Categoria')
                    ->collapsible()
                    ->getTitleFromRecordUsing(
                        function (Product $product) {
                            return ProductCategory::getName($product->category);
                        }
                    ),
            ])
            ->paginated([50, 100, 'all'])
            ->defaultPaginationPageOption(100)
            ->actions([
                Action::make('nuevo_inventario')
                    ->hiddenLabel()
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->color('success')
                    ->tooltip('Añadir inventario')
                    ->form([
                        Grid::make('')->schema([
                            TextInput::make('amount')
                                ->label('Nuevo Inventario')
                                ->placeholder('Ingrese la cantidad')
                                ->helperText('Asegúrese de ingresar el dato en kilogramos (Kg).')
                                ->required()
                                ->numeric(),
                            TextInput::make('buyprice')
                                ->label('Precio de Compra')
                                ->placeholder('Ingrese el precio de compra ($)')
                                ->required()
                                ->numeric()
                                ->reactive()
                                ->default(fn(Product $record) => $record->buyprice),
                            TextInput::make('saleprice')
                                ->label('Precio de Venta')
                                ->placeholder('Ingrese el precio de venta ($)')
                                ->required()
                                ->numeric()
                                ->reactive()
                                ->gte('buyprice')
                                ->default(fn(Product $record) => $record->saleprice),
                            Placeholder::make('helper_text')
                                ->hiddenLabel()
                                ->content('Asegúrate de validar si el precio de compra ha cambiado. Si el precio de compra ha aumentado, verifica que el precio de venta sea adecuado. Si el precio de compra disminuye, asegúrate de que el inventario del producto se agote para poder ajustar el precio.')
                                ->extraAttributes(['class' => 'font-bold text-gray-600'])
                                ->columnSpanFull()
                        ])->columns(3)
                    ])
                    ->action(function (array $data, Product $record) {
                        $record->amount += $data['amount'];

                        if ($data['buyprice'] > $record->buyprice) {
                            $record->buyprice = $data['buyprice'];
                            $record->saleprice = $data['saleprice'];
                        }
                        $record->save();
                    }),
                Action::make('update_stock')
                    ->icon('heroicon-o-lock-closed')
                    ->hiddenLabel()
                    ->color('danger')
                    ->label('Actualizar Stock')
                    /*  ->hidden(fn() => !Auth::user()->hasRole('Super Admin')) */
                    ->form([
                        Grid::make()
                            ->schema([
                                Placeholder::make('Inventario Actual')
                                    ->content(function (Product $product) {
                                        return $product->amount . ' Kg';
                                    }),
                                TextInput::make('amount')
                                    ->label('Cantidad a Disminuir')
                                    ->required()
                                    ->numeric()
                                    ->default(0)
                            ])
                    ])
                    ->action(function (array $data, Product $record) {
                        $record->amount -= $data['amount'];
                        $record->save();
                    }),
                EditAction::make('edit')
                    ->tooltip('Editar producto')
                    ->hiddenLabel()
                    ->slideOver(),
                DeleteAction::make('delete')
                    ->tooltip('Eliminar producto')
                    ->hiddenLabel()
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->deferLoading();
    }


    public static function getPages(): array
    {
        return [
            'index' => ManageProducts::route('/'),
        ];
    }
}
