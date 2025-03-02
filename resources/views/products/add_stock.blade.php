<x-modalB modalId="addStock" title="Inventario ({{ $product->name }})" modalTitle="modalTitle">
    <form id="FormAdd" action="{{ route('product.saveStock') }}" method="POST">
        @csrf
        <div class="modal-body">
            <div class="grid grid-cols-1 gap-4 px-0">
                <input type="hidden" name="id" value="{{ $product->id }}">
                <div class="grid grid-cols-1 gap-4 px-16">
                    <div class="w-full">
                        <x-jet-label value="Cantidad"></x-jet-label>
                        <x-jet-input data-name="amount" type="number" name="amount" class="w-full" required>
                        </x-jet-input>
                        <ul class="parsley-errors-list filled" data-error="amount">
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
