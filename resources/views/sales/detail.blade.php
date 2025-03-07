@php
    $table = $sale->Table;
    $table = is_null($table) ? '(Sin nombre)' : '(' . $table->name . ')';
@endphp

<x-modalB modalId="modalPayment" title="Cuenta de: {{ is_null($sale->client) ? $table : $sale->client }}"
    modalTitle="modalTitle" class="modal-lg">
    <form id="FormPayment" action="{{ route('sale.accountPayment') }}" method="POST">
        @csrf
        <input type="hidden" name="sale_id" value="{{ $sale->id }}">
        <div class="modal-body">

            @if ($sale->type == 1 && $sale->start_time)
                <p class="text-base font-semibold text-center">
                    <span>Tiempo: ${{ formatMoney($priceTime) }} ({{ $time }} minutos)</span>
                </p>
            @endif

            @if (!empty($sale->ArrayExtras))
                <table id="detailTable" class="table dt-responsive nowrap w-100">
                    <thead class="bg-secondary text-white vertical-align-middle">
                        <tr class="text-center">
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sale->ArrayExtras as $extra)
                            <tr class="text-center">
                                <td class="break-words">{{ $extra['name'] }}</td>
                                <td>{{ $extra['amount'] }}</td>
                                <td>${{ formatMoney($extra['price'] * $extra['amount']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            <div class="alert bg-secondary text-white text-xl text-center mb-0 mt-2" role="alert">
                Total a pagar: ${{ formatMoney($total) }}
            </div>
        </div>
        <div class="modal-footer">
            <x-jet-danger-button type="button" data-bs-dismiss="modal">Cerrar</x-jet-danger-button>
            <x-jet-button>Pagado</x-jet-button>
        </div>
    </form>
</x-modalB>
