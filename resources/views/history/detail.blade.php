<x-modalB modalId="modalDetail" title="Detalle del pago: {{ $historyS->client }}" modalTitle="modalTitle" class="modal-lg">
    <div class="modal-body">

        @if ($historyS->time > 0)
        <p class="text-base font-semibold text-center">
            <span>Tiempo: ${{ formatMoney($historyS->price_time) }} ({{ $historyS->time }} minutos)</span>
        </p>
    @endif

        @if (!$extras->isEmpty())
            <table id="detailTable" class="table dt-responsive nowrap w-100">
                <thead class="bg-secondary text-white vertical-align-middle">
                    <tr class="text-center">
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
                            <td>${{ formatMoney($extra->amount * $extra->price) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <div class="alert bg-secondary text-white text-xl text-center mb-0 mt-2" role="alert">
            Total cancelado: ${{ formatMoney($total) }}
        </div>
        
    </div>
    <div class="modal-footer">
        <x-jet-danger-button type="button" data-bs-dismiss="modal">Cerrar</x-jet-danger-button>
    </div>
</x-modalB>
