<x-modalB modalId="editM" title="Editar usuario" modalTitle="modalTitle">
    <form id="FormEdit" action="{{ route('user.update') }}" method="POST">
        @csrf
        <input type="hidden" value="{{ $user->id }}" name="id">
        <div class="modal-body">
            <div class="grid md:grid-cols-2 sm:grid-cols-1 gap-4 px-0">
                <div class="w-full">
                    <x-jet-label value="Nombre completo"></x-jet-label>
                    <x-jet-input data-name="name" type="text" name="name" value="{{ $user->name }}">
                    </x-jet-input>
                    <ul class="parsley-errors-list filled" data-error="name">
                    </ul>
                </div>
                <div class="w-full">
                    <x-jet-label value="Correo electronico"></x-jet-label>
                    <x-jet-input data-name="email" type="email" name="email" value="{{ $user->email }}">
                    </x-jet-input>
                    <ul class="parsley-errors-list filled" data-error="email">
                    </ul>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
    </form>
</x-modalB>
