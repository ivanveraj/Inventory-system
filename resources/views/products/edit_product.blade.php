<x-modalB modalId="edit" title="Edicion de producto ({{ $product->name }})" modalTitle="modalTitle">
    <form id="FormEdit" action="{{ route('product.update') }}" method="POST">
        @csrf
        <div class="modal-body">
            <div class="grid grid-cols-1 gap-4">
                <input type="hidden" name="id" value="{{ $product->id }}">
                <div class="grid md:grid-cols-2 sm:grid-cols-1 gap-4">
                    <div class="w-full col-span-1 sm:col-span-2">
                        <x-jet-label value="Nombre"></x-jet-label>
                        <x-jet-input data-name="name" type="text" name="name" class="w-full"
                            value="{{ $product->name }}"></x-jet-input>
                        <ul class="parsley-errors-list filled" data-error="name">
                        </ul>
                    </div>
                    <div class="w-full">
                        <x-jet-label value="Codigo"></x-jet-label>
                        <x-jet-input data-name="code" type="text" name="code" class="w-full"
                            value="{{ $product->code }}"></x-jet-input>
                        <ul class="parsley-errors-list filled" data-error="code">
                        </ul>
                    </div>
                    <div class="w-full">
                        <x-jet-label value="Precio de venta"></x-jet-label>
                        <x-jet-input data-name="saleprice" type="number" name="saleprice" class="w-full"
                            value="{{ $product->saleprice }}"></x-jet-input>
                        <ul class="parsley-errors-list filled" data-error="saleprice">
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <x-jet-danger-button type="button" data-bs-dismiss="modal">Cerrar</x-jet-danger-button>
            <x-jet-button>Guardar</x-jet-button>
        </div>
    </form>
</x-modalB>
