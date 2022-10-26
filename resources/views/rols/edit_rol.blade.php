<x-modalB modalId="edit" title="Edicion de rol ({{ $rol->name }})" modalTitle="modalTitle">
    <form id="FormEdit" action="{{ route('rol.update') }}" method="POST">
        @csrf
        <div class="modal-body">
            <div class="grid grid-cols-1 gap-4 px-0">
                <input type="hidden" name="id" value="{{ $rol->id }}">
                <div class="w-full">
                    <x-jet-label value="Nombre"></x-jet-label>
                    <x-jet-input data-name="name" type="text" name="name" value="{{ $rol->name }}"
                        class="form-control"></x-jet-input>
                    <ul class="parsley-errors-list filled" data-error="name">
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
