<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CashMovementResource\Pages;
use App\Models\CashMovement;
use App\Models\Day;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CashMovementResource extends Resource
{
    protected static ?string $model = CashMovement::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $label = 'Movimientos de Caja';
    protected static ?string $pluralLabel = 'Movimientos de Caja';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Select::make('day_id')
                    ->label('Día')
                    ->relationship('day', 'id', fn($query) => $query->orderBy('created_at', 'desc'))
                    ->getOptionLabelFromRecordUsing(fn($record) => date('d/m/Y', strtotime($record->created_at)))
                    ->required()
                    ->searchable()
                    ->preload()
                    ->default(fn() => request()->get('day_id')),
                Select::make('type')
                    ->label('Tipo')
                    ->options([
                        'income' => 'Ingreso',
                        'expense' => 'Gasto',
                    ])
                    ->required()
                    ->native(false),
                TextInput::make('description')
                    ->label('Descripción')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                TextInput::make('amount')
                    ->label('Monto')
                    ->required()
                    ->numeric()
                    ->prefix('$')
                    ->step(0.01),
                TextInput::make('category')
                    ->label('Categoría')
                    ->maxLength(255)
                    ->placeholder('Ej: Servicios, Suministros, etc.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('day.created_at')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'income' => 'success',
                        'expense' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'income' => 'Ingreso',
                        'expense' => 'Gasto',
                        default => $state,
                    }),
                TextColumn::make('description')
                    ->label('Descripción')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('category')
                    ->label('Categoría')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('amount')
                    ->label('Monto')
                    ->money('USD')
                    ->sortable()
                    ->color(fn(CashMovement $record) => $record->type === 'income' ? 'success' : 'danger'),
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCashMovements::route('/'),
        ];
    }
}

