<?php

namespace App\Filament\Resources\Settings;

use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use App\Filament\Resources\Settings\Pages\ManageSettings;
use App\Models\Setting;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-s-cog';
    protected static ?string $label = 'Configuraciones';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                TextInput::make('group')->label('Grupo')
                    ->required()->maxLength(255)->readOnly(),
                TextInput::make('key')->label('Configuración')
                    ->required()->maxLength(255)->readOnly(),
                TextInput::make('value')->label('Valor')
                    ->required(),
                Textarea::make('description')->label('Descripción')
                    ->rows(3)->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')->label('Configuración')
                    ->sortable()->searchable(),
                TextColumn::make('description')->label('Descripción')
                    ->wrap()->limit(100)
                    ->tooltip(fn($record) => $record->description),
                TextColumn::make('value')->label('Valor')
                    ->sortable(),
                TextColumn::make('updated_at')->label('Actualizado el')
                    ->since()
            ])
            ->recordActions([
                EditAction::make('edit')->hiddenLabel()
                    ->slideOver()
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageSettings::route('/'),
        ];
    }
}

