<?php

namespace App\Livewire;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Livewire\Component;

class AvatarForm extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public function mount()
    {
        $this->form->fill([
            'avatar' => auth()->user()->avatar,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                FileUpload::make('avatar')->hiddenLabel()
                    ->avatar()->image()->imageEditor()->circleCropper()
                    ->imagePreviewHeight('800')
                    ->maxSize(2048)
                    ->disk('public')
                    ->directory('avatars')
                    ->previewable()
                    ->uploadingMessage('Cargando imagen...')
                    ->afterStateUpdated(function ($state) {
                        $path = null;
                        if ($state) {
                            $path = $state->store('avatars', 'public');
                        }
                        auth()->user()->update(['avatar' => $path]);
                    })
                    ->extraAttributes(['class' => 'w-[20rem] h-[20rem]']),
            ])
            ->statePath('data');
    }

    public function render()
    {
        return view('livewire.avatar-form');
    }
}
