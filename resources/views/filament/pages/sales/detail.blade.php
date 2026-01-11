<div>
    @if (!$extras->isEmpty())
        <table class="w-full align-top border-gray-700 text-gray-300 text-sm">
            <thead class="text-left bg-gray-800">
                <tr class="px-4 py-2 font-semibold uppercase border-y border-gray-700 text-sm text-white">
                    <th class="p-2">Producto</th>
                    <th class="p-2 text-center">Cantidad</th>
                    <th class="p-2 text-center">Precio</th>
                    <th class="p-2 text-center">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($extras as $extra)
                    <tr class="border-b border-gray-700">
                        <td class="p-2 text-black">{{ $extra->Product->name }}</td>
                        <td class="p-2 flex justify-center text-black">{{ $extra->amount }}</td>
                        <td class="p-2 text-center text-black">{{ formatMoney($extra->price) }}</td>
                        <td class="p-2 flex justify-center text-black">{{ formatMoney($extra->amount * $extra->price) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="flex justify-end items-center mt-6">
        @if ($sale->type == 1 && $sale->start_time)
            <div>
                <h2>Hora Inicio {{ $sale->start_time }}</h2>
                <h2 class="text-lg font-bold">Tiempo: {{ $time }} (minutos) = {{ formatMoney($priceTime) }}</h2>
            </div>
        @endif
    </div>
    <div class="flex justify-end items-center mt-4">
        <h2 class="text-lg font-bold text-danger-500">Total a pagar: {{ formatMoney($total) }}</h2>
    </div>
</div>
