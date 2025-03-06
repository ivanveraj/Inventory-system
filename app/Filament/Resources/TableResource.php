<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TableResource\Pages;
use App\Models\Table as TableModel;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TableResource extends Resource
{
    protected static ?string $model = TableModel::class;
    protected static ?string $navigationIcon = 'heroicon-s-table-cells';
    protected static ?string $label = 'Mesas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nombre')
                    ->placeholder('Ingresa el nombre...')
                    ->required()
                    ->maxLength(255),
                Select::make('state')
                    ->label('Estado')
                    ->options([
                        1 => 'Activo',
                        0 => 'Inactivo',
                    ])
                    ->default(1)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('state')
                    ->label('Estado')
                    ->badge()
                    ->color(fn(int $state): string => match ($state) {
                        0 => 'danger',
                        1 => 'success',
                        2 => 'warning'
                    })
                    ->formatStateUsing(function ($state) {
                        switch ($state) {
                            case 0:
                                return 'Inactivo';
                            case 1:
                                return 'Activo';
                        }
                    })
                    ->sortable(),
            ])
            ->actions([
                EditAction::make()
                    ->tooltip('Editar producto')
                    ->hiddenLabel(),
                DeleteAction::make()
                    ->tooltip('Eliminar producto')
                    ->hiddenLabel(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTables::route('/'),
        ];
    }
}
