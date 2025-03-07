<x-modalB modalId="create" title="Creacion de usuario" modalTitle="modalTitle">
    <form id="FormCreate" action="{{ route('user.store') }}" method="POST">
        @csrf
        <div class="modal-body">
            <div class="grid md:grid-cols-2 sm:grid-cols-1 gap-4 px-0">
                <div class="w-full">
                    <x-jet-label value="Nombre completo"></x-jet-label>
                    <x-jet-input data-name="name" type="text" name="name"></x-jet-input>
                    <ul class="parsley-errors-list filled" data-error="name">
                    </ul>
                </div>
                <div class="w-full">
                    <x-jet-label value="Usuario"></x-jet-label>
                    <x-jet-input data-name="user" type="text" name="user"></x-jet-input>
                    <ul class="parsley-errors-list filled" data-error="user"></ul>
                </div>
                <div class="w-full">
                    <x-jet-label value="Contraseña"></x-jet-label>
                    <x-jet-input data-name="password" type="password" name="password"></x-jet-input>
                    <ul class="parsley-errors-list filled" data-error="password">
                    </ul>
                </div>
                <div class="w-full">
                    <x-jet-label value="Confirmar contraseña"></x-jet-label>
                    <x-jet-input data-name="passwordC" type="password" name="passwordC"></x-jet-input>
                    <ul class="parsley-errors-list filled" data-error="passwordC">
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
