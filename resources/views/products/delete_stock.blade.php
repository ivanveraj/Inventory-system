<x-modalB modalId="deleteStock" title="Eliminacion de inventario ({{ $product->name }})" modalTitle="modalTitle">
    <form id="FormDelete" action="{{ route('product.saveDeleteStock') }}" method="POST">
        @csrf
        @php
            $LogUser=Auth::user();
        @endphp
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
                    @if ($LogUser->id != 1)
                        <div class="w-full">
                            <x-jet-label value="Justificación"></x-jet-label>
                            <textarea data-name="description" name="description" class="form-control" rows="5"></textarea>
                            <ul class="parsley-errors-list filled" data-error="description">
                            </ul>
                        </div>
                    @endif

                </div>
            </div>
        </div>
        <div class="modal-footer">
            <x-jet-danger-button type="button" data-bs-dismiss="modal">Cerrar</x-jet-danger-button>
            <x-jet-button>Guardar</x-jet-button>
        </div>
    </form>
</x-modalB>
