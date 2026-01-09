<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\UserResource\Pages\ManageUsers;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Enums\FiltersLayout;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-s-users';
    protected static ?string $label = 'Usuarios';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->label('Nombre Completo')
                    ->placeholder('Ingrese el nombre completo')
                    ->required()->maxLength(255),
                TextInput::make('username')->label('Usuario')
                    ->placeholder('Ingrese el usuario')
                    ->required()->unique(ignoreRecord: true),
                TextInput::make('phone_number')->label('Número de Teléfono')
                    ->tel()->maxLength(20)
                    ->hintIcon(...hint_info_tooltip('Formato: +1234567890 o 1234567890')),
                Select::make('role')->label('Rol')
                    ->relationship('roles', 'name')
                    ->required()->preload()->searchable()->multiple(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')
                    ->sortable()->searchable()->toggleable(),
                TextColumn::make('name')->label('Nombre')
                    ->searchable()->sortable()->weight('bold'),
                TextColumn::make('username')->label('Usuario')
                    ->searchable()->sortable(),
                TextColumn::make('phone_number')->label('Teléfono')
                    ->searchable()->sortable()->placeholder('—')
                    ->alignCenter()->toggleable(),
                TextColumn::make('roles.name')->label('Rol')
                    ->badge()->searchable()
                    ->color('primary')
                    ->formatStateUsing(fn($record) => $record->roles->pluck('name')->join(', ')),
                ToggleColumn::make('status')->label('Estado'),
                TextColumn::make('created_at')->label('Creado el')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()->toggleable(),
                TextColumn::make('updated_at')->label('Actualizado el')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')->label('Estado')
                    ->options([
                        1 => 'Activo',
                        0 => 'Inactivo',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'] !== null,
                            fn(Builder $query, $value): Builder => $query->where('status', $value),
                        );
                    }),
            ], layout: FiltersLayout::AboveContent)
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                EditAction::make('edit')
                    ->label('Editar')
                    ->tooltip('Editar usuario')
                    ->icon('heroicon-o-pencil')
                    ->slideOver()
                    ->modalHeading('Editar Usuario'),
                DeleteAction::make('delete')
                    ->label('Eliminar')
                    ->tooltip('Eliminar usuario')
                    ->icon('heroicon-o-trash')
                    ->requiresConfirmation(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Eliminar seleccionados'),
                ]),
            ])
            ->emptyStateHeading('No hay usuarios')
            ->emptyStateDescription('Comience creando su primer usuario.')
            ->emptyStateIcon('heroicon-o-users');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageUsers::route('/'),
        ];
    }
}
