<x-jet-form-section submit="updateProfileInformation">
    <x-slot name="title">
        {{ __('Profile Information') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Update your account\'s profile information and email address.') }}
    </x-slot>

    <x-slot name="form">
        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
            <div x-data="{ photoName: null, photoPreview: null }" class="col-span-6 sm:col-span-4">
                <input type="file" class="hidden" wire:model="photo" x-ref="photo" accept="image/*"
                    x-on:change="
                                    photoName = $refs.photo.files[0].name;
                                    const reader = new FileReader();
                                    reader.onload = (e) => {
                                        photoPreview = e.target.result;
                                    };
                                    reader.readAsDataURL($refs.photo.files[0]);
                            " />

                <x-jet-label for="photo" value="Foto de perfil" class="text-center" />

                <div class="flex justify-center">
                    <div>
                        <div class="mt-2 flex justify-center" x-show="! photoPreview">
                            <img src="{{ $this->user->profile_photo_url }}" alt="{{ $this->user->name }}"
                                class="rounded-full h-20 w-20 object-cover">
                        </div>

                        <div class="mt-2 flex justify-center" x-show="photoPreview" style="display: none;">
                            <span class="block rounded-full w-20 h-20 bg-cover bg-no-repeat bg-center"
                                x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                            </span>
                        </div>

                       {{--  <x-jet-secondary-button class="my-3" type="button" x-on:click.prevent="$refs.photo.click()">
                            Seleccione una nuevo foto de perfil
                        </x-jet-secondary-button>

                        @if ($this->user->profile_photo_path)
                            <x-jet-danger-button type="button" class="mt-2" wire:click="deleteProfilePhoto">
                                Eliminar foto
                            </x-jet-danger-button>
                        @endif --}}
                    </div>
                </div>

                <x-jet-input-error for="photo" class="mt-2" />
            </div>
        @endif

        <!-- Name -->
        <div class="px-12">
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <x-jet-label for="name" value="Nombre" />
                    <x-jet-input id="name" type="text" class="mt-1 block w-full" wire:model.defer="state.name"
                        autocomplete="name" />
                    <x-jet-input-error for="name" class="mt-2" />
                </div>
                <div>
                    <x-jet-label for="user" value="Usuario" />
                    <x-jet-input id="user" type="text" class="mt-1 block w-full"
                        wire:model.defer="state.user" />
                    <x-jet-input-error for="user" class="mt-2" />
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
