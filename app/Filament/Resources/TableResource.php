<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use App\Filament\Resources\TableResource\Pages\ManageTables;
use App\Models\Table as TableModel;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class TableResource extends Resource
{
    protected static ?string $model = TableModel::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-s-table-cells';
    protected static ?string $label = 'Mesas';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->label('Nombre')
                    ->placeholder('Ingresa el nombre...')
                    ->required()->maxLength(255)->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->withoutTrashed())
            ->columns([
                TextColumn::make('id')->label('ID')
                    ->sortable(),
                TextColumn::make('name')->label('Nombre')
                    ->searchable()->sortable(),
                ToggleColumn::make('state')->label('Estado'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageTables::route('/'),
        ];
    }
}
