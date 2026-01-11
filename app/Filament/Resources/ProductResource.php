<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Schemas\Components\Grid;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use App\Enums\ProductCategory;
use App\Filament\Resources\ProductResource\Pages\ManageProducts;
use App\Models\Product;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Filament\Tables\Columns\ToggleColumn;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-table-cells';
    protected static ?string $label = 'Inventario';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                TextInput::make('name')->label('Nombre del Producto')
                    ->placeholder('Ingrese el nombre del producto')
                    ->required()->maxLength(255),
                TextInput::make('sku')->label('Código SKU')
                    ->placeholder('Ingrese el código SKU único')
                    ->required()->maxLength(255)->reactive()->unique(ignoreRecord: true)
                    ->formatStateUsing(fn($state) => strtoupper($state))
                    ->hintIcon(...hint_info_tooltip('El SKU se mostrará en mayúsculas automáticamente.')),
                Select::make('category')->label('Categoria')
                    ->placeholder('Seleccione la categoria del producto')
                    ->options(ProductCategory::class)
                    ->required()->searchable(),
                RichEditor::make('description')->label('Descripción')
                    ->columnSpanFull(),
                TextInput::make('buyprice')->label('Precio de Compra')
                    ->placeholder('Ingrese el precio de compra (ej. 1500)')
                    ->required()->numeric()->reactive(),
                TextInput::make('saleprice')->label('Precio de Venta')
                    ->placeholder('Ingrese el precio de venta (ej. 2000)')
                    ->required()->numeric()->reactive()->gte('buyprice'),
                TextInput::make('amount')->label('Cantidad en Inventario')
                    ->placeholder('Ingrese la cantidad disponible')
                    ->required()->numeric()->disabledOn('edit'),
                Toggle::make('is_activated')->label('Activado')
                    ->default(true)->inline(false),
                Toggle::make('has_stock_alert')->label('Activar Alerta de Stock')
                    ->default(true)->reactive()->inline(false),
                TextInput::make('min_stock_alert')->label('Alerta de Stock Mínimo')
                    ->placeholder('Ingrese la cantidad mínima para la alerta')
                    ->required()->numeric()->reactive()
                    ->hidden(fn($get) => !$get('has_stock_alert')),
                TextInput::make('barcode')->label('Código de Barras')
                    ->placeholder('Ingrese el código de barras del producto')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Producto')
                    ->searchable()->sortable(),
                TextColumn::make('sku')->label('SKU')
                    ->searchable()->sortable(),
                TextColumn::make('amount')->label('Cantidad')
                    ->alignCenter()->sortable()
                    ->color(fn($record) => $record->has_stock_alert && $record->amount < $record->min_stock_alert ? 'danger' : null),
                TextColumn::make('buyprice')->label('Compra')
                    ->formatStateUsing(fn($state) => formatMoney($state))
                    ->toggleable(isToggledHiddenByDefault: true)->sortable(),
                TextColumn::make('saleprice')->label('Venta')
                    ->html()->sortable()
                    ->formatStateUsing(fn($state) => formatMoney($state)),
                ToggleColumn::make('is_activated')->label('Estado')
                    ->toggleable()->sortable(),
                TextColumn::make('created_at')->label('Creado el')
                    ->dateTime()->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->label('Actualizado el')
                    ->dateTime()->sortable()
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
                    ->getTitleFromRecordUsing(fn(Product $product) => ProductCategory::getName($product->category)),
            ])
            ->paginated([50, 100, 'all'])
            ->defaultPaginationPageOption(100)
            ->recordActions([
                Action::make('nuevo_inventario')->hiddenLabel()
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->color('success')
                    ->modalHeading('Añadir inventario')
                    ->modalSubmitActionLabel('Añadir')
                    ->modalCancelActionLabel('Cancelar')
                    ->tooltip('Añadir inventario')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('amount')->label('Nuevo Inventario')
                                ->placeholder('Ingrese la cantidad')
                                ->hintIcon(...hint_info_tooltip('Asegúrese de ingresar el dato en kilogramos (Kg).'))
                                ->required()->numeric(),
                            TextInput::make('buyprice')->label('Precio de Compra')
                                ->placeholder('Ingrese el precio de compra ($)')
                                ->required()->numeric()->reactive()
                                ->default(fn($record) => $record->buyprice),
                            TextInput::make('saleprice')->label('Precio de Venta')
                                ->placeholder('Ingrese el precio de venta ($)')
                                ->required()->numeric()->reactive()->gte('buyprice')
                                ->default(fn($record) => $record->saleprice),
                            Placeholder::make('helper_text')->hiddenLabel()
                                ->content('Asegúrate de validar si el precio de compra ha cambiado. Si el precio de compra ha aumentado, verifica que el precio de venta sea adecuado. Si el precio de compra disminuye, asegúrate de que el inventario del producto se agote para poder ajustar el precio.')
                                ->extraAttributes(['class' => 'font-bold text-gray-600'])
                                ->columnSpanFull(),
                        ])
                    ])
                    ->action(function (array $data, Product $record) {
                        $record->amount += $data['amount'];

                        if ($data['buyprice'] > $record->buyprice) {
                            $record->buyprice = $data['buyprice'];
                            $record->saleprice = $data['saleprice'];
                        }
                        $record->save();
                    }),
                Action::make('update_stock')->icon('heroicon-o-lock-closed')
                    ->hiddenLabel()
                    ->color('danger')
                    ->label('Actualizar Stock')
                    /*  ->hidden(fn() => !Auth::user()->hasRole('Super Admin')) */
                    ->schema([
                        Grid::make()
                            ->schema([
                                Placeholder::make('Inventario Actual')
                                    ->content(function (Product $product) {
                                        return $product->amount . ' Unidades';
                                    }),
                                TextInput::make('amount')->label('Cantidad a Disminuir')
                                    ->required()->numeric()->default(0)
                            ])
                    ])
                    ->action(function (array $data, Product $record) {
                        $record->amount -= $data['amount'];
                        $record->save();
                    }),
                EditAction::make('edit')->slideOver(),
                DeleteAction::make('delete')
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
