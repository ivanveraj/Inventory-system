<x-modalB modalId="modalDetail" title="Detalle del pago" modalTitle="modalTitle" class="modal-lg">
    <div class="modal-body">
        <div class="my-3">
            <h5 class="text-center">{{ $historyS->client }}</h5>
        </div>
        @if (!$extras->isEmpty())
            <table id="detailTable" class="w-full mb-0 align-top border-gray-200 text-slate-900">
                <thead class="text-center">
                    <tr
                        class="px-6 py-3 font-bold uppercase align-middle border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-900 opacity-70">
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($extras as $extra)
                        <tr class="text-center">
                            @php
                                $product = $extra->Product;
                            @endphp
                            <td class="break-words">{{ is_null($product) ? 'Sin nombre' : $product->name }}</td>
                            <td>{{ $extra->amount }}</td>
                            <td>{{ formatMoney($extra->amount * $extra->price) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <div class="flex justify-between items-center mt-4">
            <div>
                @if ($historyS->time > 0)
                    <h5>Tiempo: {{ $historyS->time }} (minutos)</h5>
                    <h5>Precio tiempo: {{ formatMoney($historyS->price_time) }}</h5>
                @endif
            </div>
            <div>
                <h5>Total pagado: {{ formatMoney($total) }}</h5>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <x-jet-danger-button type="button" data-bs-dismiss="modal">Cerrar</x-jet-danger-button>
    </div>
</x-modalB>
