<x-filament-panels::page class="profile-page">
    @livewire('avatar-form')

    <form wire:submit="save">
        {{ $this->form }}

        <div class="flex justify-center mt-6">
            <x-filament::button wire:click="save" size="lg">
                Actualizar
            </x-filament::button>
        </div>
    </form>

    <x-filament-actions::modals />
</x-filament-panels::page>
