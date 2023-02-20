<x-modalB modalId="create" title="Creacion de producto" modalTitle="modalTitle">
    <form id="FormCreate" action="{{ route('product.store') }}" method="POST">
        @csrf
        <div class="modal-body">
            <div class="grid md:grid-cols-2 sm:grid-cols-1 gap-4 px-4">
                <div class="w-full col-span-1 md:col-span-2">
                    <x-jet-label value="Nombre"></x-jet-label>
                    <x-jet-input data-name="name" type="text" name="name" class="w-full"></x-jet-input>
                    <ul class="parsley-errors-list filled" data-error="name">
                    </ul>
                </div>
                <div class="w-full">
                    <x-jet-label value="Codigo"></x-jet-label>
                    <x-jet-input data-name="code" type="text" name="code" class="w-full"></x-jet-input>
                    <ul class="parsley-errors-list filled" data-error="code">
                    </ul>
                </div>
                <div class="w-full">
                    <x-jet-label value="Precio de compra"></x-jet-label>
                    <x-jet-input data-name="buyprice" type="number" name="buyprice" class="w-full"></x-jet-input>
                    <ul class="parsley-errors-list filled" data-error="buyprice">
                    </ul>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <x-jet-danger-button type="button" data-bs-dismiss="modal">Cerrar</x-jet-danger-button>
            <x-jet-button>Guardar</x-jet-button>
        </div>
    </form>
</x-modalB>
