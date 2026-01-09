<?php

namespace App\Filament\Resources\Roles;

use App\Filament\Resources\Roles\Pages\ManageRoles;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Models\Role;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;
    protected static ?string $recordTitleAttribute = 'Role';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->label('Nombre')
                    ->required()->columnSpanFull()->unique(ignoreRecord: true)
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->where('name', '!=', 'SuperAdmin'))
            ->columns([
                TextColumn::make('id')->label('ID')
                    ->searchable()->sortable(),
                TextColumn::make('name')->label('Nombre')
                    ->searchable()->sortable(),
                TextColumn::make('created_at')->label('Creado el')
                    ->dateTime()->sortable(),
                TextColumn::make('updated_at')->label('Actualizado el')
                    ->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()->modalHeading('Eliminar Rol'),
            ])
        ;
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageRoles::route('/'),
        ];
    }
}
