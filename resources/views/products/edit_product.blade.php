<x-modalB modalId="edit" title="Edicion de producto ({{ $product->name }})" modalTitle="modalTitle">
    <form id="FormEdit" action="{{ route('product.update') }}" method="POST">
        @csrf
        <div class="modal-body">
            <div class="grid grid-cols-1 gap-4 px-0">
                <input type="hidden" name="id" value="{{ $product->id }}">
                <div class="grid md:grid-cols-2 sm:grid-cols-1 gap-4">
                    <div class="w-full">
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
                        <x-jet-label value="Precio de compra"></x-jet-label>
                        <x-jet-input data-name="buyprice" type="number" name="buyprice" class="w-full"
                            value="{{ $product->buyprice }}"></x-jet-input>
                        <ul class="parsley-errors-list filled" data-error="buyprice">
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
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
    </form>
</x-modalB>
