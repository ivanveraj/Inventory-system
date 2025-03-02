<x-modalB modalId="create" title="Creacion de rol" modalTitle="modalTitle">
    <form id="FormCreate" action="{{ route('rol.store') }}" method="POST">
        @csrf
        <div class="modal-body">
            <div class="grid grid-cols-1 gap-4 px-0">
                <div class="w-full">
                    <x-jet-label value="Nombre"></x-jet-label>
                    <x-jet-input data-name="name" type="text" name="name" class="w-full"></x-jet-input>
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
