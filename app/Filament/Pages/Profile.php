<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use App\Traits\NotificationTrait;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Textarea;

class Profile extends Page implements HasSchemas
{
    use InteractsWithSchemas, NotificationTrait;

    protected string $view = 'filament.pages.profile';
    protected static ?string $title = 'Perfil';

    public $user;
    public ?array $data = [];

    public function mount(): void
    {
        $this->user = Auth::user();
        $this->form->fill([
            'name' => $this->user->name,
            'username' => $this->user->username,
            'email' => $this->user->email,
            'phone_number' => $this->user->phone_number,
            'avatar' => $this->user->avatar,
            'description' => $this->user->description,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Información Personal')
                    ->description('Actualiza tu información personal')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')->label('Nombre')
                            ->placeholder('Escribe tu nombre...')
                            ->required(),
                        TextInput::make('username')->label('Usuario')
                            ->readOnly(),
                        TextInput::make('email')->label('Correo electrónico')
                            ->placeholder('Escribe tu correo electrónico...')
                            ->unique(ignoreRecord: true)->email(),
                        TextInput::make('phone_number')->label('Teléfono')
                            ->placeholder('Escribe tu teléfono...')
                            ->tel()->maxLength(10)->numeric()
                            ->unique(ignoreRecord: true)
                            ->rule('regex:/^\+?[0-9]{7,15}$/'),
                        Textarea::make('description')->label('Sobre mí')
                            ->placeholder('Escribe sobre ti...')
                            ->rows(4)->columnSpanFull(),
                    ]),
                Section::make('Seguridad')
                    ->description('Actualiza tu contraseña')
                    ->columns(2)
                    ->schema([
                        TextInput::make('current_password')->label('Contraseña actual')
                            ->placeholder('Escribe tu contraseña actual...')
                            ->password()->revealable()->columnSpanFull(),
                        TextInput::make('new_password')
                            ->label('Nueva contraseña')
                            ->placeholder('Escribe tu nueva contraseña...')
                            ->password()->revealable()
                            ->minLength(8)
                            ->same('confirm_password'),
                        TextInput::make('confirm_password')
                            ->label('Confirmar nueva contraseña')
                            ->placeholder('Escribe tu nueva contraseña...')
                            ->password()->revealable()
                            ->requiredWith('new_password'),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $user = auth()->user();

        // Preparamos siempre los datos básicos (sin username y campos de seguridad)
        $updateData = Arr::except($data, [
            'username',
            'current_password',
            'confirm_password',
            'new_password',
        ]);

        // Si el usuario quiere cambiar la contraseña
        if (! empty($data['current_password']) || ! empty($data['new_password'])) {
            // 1. Validar contraseña actual
            if (! Hash::check($data['current_password'], $user->password)) {
                $this->addError('current_password', 'La contraseña actual no es correcta.');
                return;
            }

            // 2. Validar que haya nueva contraseña
            if (! empty($data['new_password'])) {
                $updateData['password'] = Hash::make($data['new_password']);
            }
        }

        // 3. Actualizar usuario
        $user->update($updateData);

        $this->resetErrorBag();
        $this->customNotification('success', 'Perfil actualizado correctamente.');
    }
}
