<?php

namespace App\Filament\Resources\Suppliers;

use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\CreateAction;
use App\Filament\App\Resources\Suppliers\Pages\ManageSuppliers;
use App\Models\Supplier;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Enums\ProductCategory;
use Filament\Tables\Grouping\Group;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $label = 'Proveedores';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->label('Empresa')
                    ->placeholder('Nombre de la Empresa o Distribuidora')
                    ->required()->maxLength(255)->columnSpanFull(),
                Select::make('category')->label('Categoría')
                    ->placeholder('Selecciona una categoría')
                    ->searchable()->required()
                    ->options(ProductCategory::class),
                TextInput::make('contact_person')->label('Nombre Vendedor')
                    ->placeholder('Nombre de la persona de contacto')
                    ->maxLength(255),
                TextInput::make('email')->label('Correo Electrónico')
                    ->placeholder('Introduce el correo electrónico')
                    ->email()->maxLength(255),
                TextInput::make('phone')->label('Teléfono')
                    ->placeholder('Introduce el número de teléfono')
                    ->tel()->required()->maxLength(255),
                TextInput::make('address')->label('Dirección')
                    ->maxLength(255)
                    ->placeholder('Introduce la dirección'),
                Select::make('schedule')->label('Fecha de pedido')
                    ->multiple()->searchable()
                    ->options([
                        'Monday' => 'Lunes',
                        'Tuesday' => 'Martes',
                        'Wednesday' => 'Miércoles',
                        'Thursday' => 'Jueves',
                        'Friday' => 'Viernes',
                        'Saturday' => 'Sábado',
                        'Sunday' => 'Domingo',
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->groups([
                Group::make('category')->label('Categoria')->collapsible()
                    ->getTitleFromRecordUsing(fn($record) => ProductCategory::getName($record->category)),
            ])
            ->columns([
                TextColumn::make('name')->label('Proveedor')
                    ->html()->searchable()->sortable()
                    ->formatStateUsing(function ($record) {
                        $name = $record->name ? 'Empresa: ' . $record->name . '<br>' : '';
                        $email = $record->email ? 'Email: ' . $record->email : '';
                        return "{$name} {$email}";
                    }),
                TextColumn::make('contact_person')->label('Vendedor')
                    ->searchable()->sortable()->toggleable(),
                TextColumn::make('phone')->label('Teléfono')
                    ->searchable()->toggleable()->alignCenter(),
                TextColumn::make('schedule')->label('Horario')
                    ->html()
                    ->formatStateUsing(function ($state) {
                        $dayTranslations = [
                            'Monday' => 'Lunes',
                            'Tuesday' => 'Martes',
                            'Wednesday' => 'Miércoles',
                            'Thursday' => 'Jueves',
                            'Friday' => 'Viernes',
                            'Saturday' => 'Sábado',
                            'Sunday' => 'Domingo',
                        ];

                        $days = json_decode($state, true);

                        // Verificar si $days es un array y traducir cada día
                        if (is_array($days)) {
                            $translatedDays = array_map(function ($day) use ($dayTranslations) {
                                return $dayTranslations[$day] ?? $day; // Traducir, o usar el original si no existe
                            }, $days);

                            // Unir los días traducidos con un salto de línea HTML
                            return implode('<br>', array_map('strip_tags', $translatedDays));
                        }

                        // Devolver el estado original si no se pudo decodificar
                        return $state;
                    })
                    ->searchable(),
                TextColumn::make('address')->label('Dirección')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')->label('Fecha de Creación')
                    ->dateTime()->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->label('Fecha de Actualización')
                    ->dateTime()->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                EditAction::make()->modalHeading('Editar Proveedor')
                    ->slideOver()
                    ->beforeFormFilled(function ($record) {
                        if (isset($record->schedule) && is_string($record->schedule)) {
                            $record->schedule = json_decode($record->schedule, true);
                        }
                    }),
                DeleteAction::make()
            ])
            ->emptyStateActions([
                CreateAction::make('create')->label('Añadir proveedor')
                    ->slideOver()->modalHeading('Crear Proveedor')
                    ->mutateDataUsing(function (array $data): array {
                        if (isset($data['schedule']) && is_array($data['schedule'])) {
                            $data['schedule'] = json_encode($data['schedule']);
                        }
                        return $data;
                    }),
            ])
            ->emptyStateIcon('heroicon-o-archive-box')
            ->emptyStateHeading('No hay proveedores')
            ->emptyStateDescription('No se encontraron proveedores en la base de datos. Añade un nuevo proveedor para comenzar.');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageSuppliers::route('/'),
        ];
    }
}
