<x-modalB modalId="modalPayment" title="Detalle del pago" modalTitle="modalTitle" class="modal-lg">
    <form id="FormPayment" action="{{ route('sale.accountPayment') }}" method="POST">
        @csrf
        <input type="hidden" name="sale_id" value="{{ $sale->id }}">
        <div class="modal-body">
            @php
                $table = $sale->Table;
                $table = is_null($table) ? 'Sin nombre' : $table->name;
            @endphp
            <div class="my-3">
                <h5 class="text-center">{{ is_null($sale->client) ? $table : 'Cuenta de: ' . $sale->client }}</h5>
            </div>
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
                            <td>{{ formatMoney($extra->price) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="flex justify-between items-center mt-4">
                <div>
                    @if ($sale->type == 1 && $sale->start_time)
                        <h5>Tiempo: {{ $time }} (minutos)</h5>
                        <h5>Precio tiempo: {{ formatMoney($priceTime) }}</h5>
                    @endif
                </div>
                <div>
                    <h5>Total a pagar: {{ formatMoney($total) }}</h5>
                </div>
            </div>

        </div>
      {{--   <div class="modal-footer">
            <x-jet-secondary-button type="button" data-bs-dismiss="modal">Cerrar</x-jet-secondary-button>
            <x-jet-button>Pagado</x-jet-button>
        </div> --}}
    </form>
</x-modalB>
