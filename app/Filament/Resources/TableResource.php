<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use App\Filament\Resources\TableResource\Pages\ManageTables;
use App\Enums\TableType;
use App\Models\Table as TableModel;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Enums\FiltersLayout;

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
                    ->placeholder('Ingresa el nombre de la mesa')
                    ->required()->maxLength(255),
                Select::make('type')->label('Tipo')
                    ->options(TableType::class)
                    ->default(TableType::WITH_TIME->value)
                    ->required()->selectablePlaceholder(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->withoutTrashed())
            ->paginated(false)
            ->columns([
                TextColumn::make('id')->label('ID')
                    ->sortable(),
                TextColumn::make('name')->label('Nombre')
                    ->searchable()->sortable(),
                TextColumn::make('type')->label('Tipo')
                    ->formatStateUsing(function ($state) {
                        if ($state instanceof TableType) {
                            return $state->getLabel();
                        }
                        return TableType::tryFrom($state)?->getLabel() ?? TableType::WITH_TIME->getLabel();
                    }),
                ToggleColumn::make('state')->label('Estado'),
            ])
            ->recordActions([
                EditAction::make()
                    ->modalHeading('Editar Mesa'),
                DeleteAction::make(),
            ])
            ->filters([
                SelectFilter::make('type')->label('Tipo')
                    ->options(TableType::class),
            ], layout: FiltersLayout::AboveContent);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageTables::route('/'),
        ];
    }
}
