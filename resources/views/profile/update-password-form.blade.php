<x-jet-form-section submit="updatePassword">
    <x-slot name="form">
        <div class="px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 gap-4">
                <div class="sm:col-span-2 md:col-span-2">
                    <x-jet-label for="current_password" value="Contraseña actual" />
                    <x-jet-input id="current_password" type="password" class="mt-1 block w-full"
                        wire:model.defer="state.current_password" autocomplete="current-password" />
                    <x-jet-input-error for="current_password" class="mt-2" />
                </div>
                <div>
                    <x-jet-label for="password" value="Nueva contraseña" />
                    <x-jet-input id="password" type="password" class="mt-1 block w-full"
                        wire:model.defer="state.password" autocomplete="new-password" />
                    <x-jet-input-error for="password" class="mt-2" />
                </div>
                <div>
                    <x-jet-label for="password_confirmation" value="Confirmar contraseña" />
                    <x-jet-input id="password_confirmation" type="password" class="mt-1 block w-full"
                        wire:model.defer="state.password_confirmation" autocomplete="new-password" />
                    <x-jet-input-error for="password_confirmation" class="mt-2" />
                </div>
            </div>
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-jet-action-message class="mr-3" on="saved">
            Guardado
        </x-jet-action-message>
        <x-jet-button wire:loading.attr="disabled" wire:target="photo">
            Guardar
        </x-jet-button>
    </x-slot>
</x-jet-form-section>
