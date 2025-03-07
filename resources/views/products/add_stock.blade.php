<x-modalB modalId="addStock" title="Historial ({{ $product->name }})" modalTitle="modalTitle" class="modal-lg">
    <form id="FormAdd" action="{{ route('product.saveStock') }}" method="POST">
        @csrf
        <div class="modal-body">
            <input type="hidden" name="id" value="{{ $product->id }}">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 px-12">
                <div class="w-full">
                    <x-jet-label value="Cantidad"></x-jet-label>
                    <x-jet-input data-name="amount" type="number" name="amount" class="w-full" placeholder="0"
                        required>
                    </x-jet-input>
                    <ul class="parsley-errors-list filled" data-error="amount">
                    </ul>
                </div>
                <div class="w-full">
                    <x-jet-label value="Precio de compra"></x-jet-label>
                    <x-jet-input data-name="buyprice" type="number" name="buyprice" class="w-full"
                        value="{{ $buyprice }}" required>
                    </x-jet-input>
                    <ul class="parsley-errors-list filled" data-error="buyprice">
                    </ul>
                </div>
            </div>

            @if (!$historyProducts->isEmpty())
                <div class="mt-4 w-full">
                    <table id="history_price" class="table dt-responsive table-responsive nowrap w-100">
                        <thead class="bg-secondary text-white vertical-align-middle">
                            <tr class="text-center">
                                <th>Cantidad</th>
                                <th>Precio compra</th>
                                <th>Precio venta</th>
                                <th>Utilidad</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($historyProducts as $historyP)
                                <tr class="text-center">
                                    <td>{{ $historyP->amount }}</td>
                                    <td>${{ formatMoney($historyP->buyprice) }}</td>
                                    <td>${{ formatMoney($product->saleprice) }}</td>
                                    <td>${{ formatMoney($product->saleprice - $historyP->buyprice) }}</td>
                                    <td>{{ date_format($historyP->created_at, 'd-M-Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
        <div class="modal-footer">
            <x-jet-danger-button type="button" data-bs-dismiss="modal">Cerrar</x-jet-danger-button>
            <x-jet-button>Guardar</x-jet-button>
        </div>
    </form>
</x-modalB>
