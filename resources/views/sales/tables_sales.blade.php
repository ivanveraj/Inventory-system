<table id="sales" class="items-center w-full mb-0 align-top border-gray-200 text-slate-500">
    <thead class="text-center">
        <tr
            class="px-6 py-3 font-bold uppercase align-middle border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">
            <th>Mesa</th>
            <th>Tiempo</th>
            <th>Extras</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody class="text-center">
        @foreach ($sales as $sale)
            @php
                $table = $sale->Table;
                $extras = $sale->Extras;
                if (is_null($sale->start_time)) {
                    $total = 0;
                } else {
                    $time = DateDifference(date('Y-m-d H:i:s'), $sale->start_time);
                    if ($time < $TiempoMinimo) {
                        $total = $PrecioMinimo;
                    } else {
                        $PrecioXHora = $PrecioXHora;
                        $total = round(($PrecioXHora / 60) * $time);
                    }
                }
                
                foreach ($extras as $e) {
                    $total += $e->total;
                }
            @endphp
            <tr>
                <td class="p-2 align-middle bg-transparent border-b">
                    {{ is_null($table) ? 'Tabla sin nombre' : $table->name }}</td>
                <td class="p-2 align-middle bg-transparent border-b">
                    <div>
                        <h6>{{ is_null($sale->start_time) ? '' : DateDifference(date('Y-m-d H:i:s'), $sale->start_time) . ' minutos' }}
                        </h6>
                        <x-jet-button type="button" onclick="startTime({{ $sale->id }})"
                            class="startTime_{{ $sale->id }} {{ is_null($sale->start_time) ? '' : 'hidden' }}">
                            Iniciar
                        </x-jet-button>
                    </div>
                </td>
                <td class="p-2 align-middle bg-transparent border-b">
                    <div class="flex justify-center">
                        <form id="formAddProduct_{{ $sale->id }}" action="{{ route('sale.addProduct') }}"
                            method="POST">
                            @csrf
                            <input type="hidden" value="{{ $sale->id }}" name="sale_id">
                            <div class="flex justify-center items-center {{ $extras->isEmpty() ? '' : 'mb-3' }}">
                                <div class="w-2/3">
                                    <x-jet-input type="number" name="amount" class="w-full" placeholder="Cantidad">
                                    </x-jet-input>
                                </div>
                                <div class="w-full ml-3">
                                    <div class="flex items-center">
                                        <div>
                                            <select id="selectProduct_{{ $sale->id }}" name="product_id"
                                                class="form-control" style="max-width: 250px !important"></select>
                                        </div>
                                        <div>
                                            <button type="submit" class="btn btn-secondary w-full ml-2">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    @if (!$extras->isEmpty())
                        <table class="w-full mb-0 align-top border-gray-200 text-slate-900">
                            <thead class="text-center">
                                <tr
                                    class="px-6 py-3 font-bold uppercase align-middle border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Precio</th>
                                    <th>Accion</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($extras as $extra)
                                    <tr>
                                        <td class="break-words">{{ $extra->Product->name }}</td>
                                        <td>
                                            <x-jet-input type="number" id="amountExtra_{{ $extra->id }}"
                                                class="w-full" value="{{ $extra->amount }}">
                                            </x-jet-input>
                                        </td>
                                        <td id="priceExtra_{{ $extra->id }}" data-price="{{ $extra->price }}">
                                            {{ $extra->price }}</td>
                                        <td>
                                            <x-jet-danger-button type="button"
                                                onclick="deleteExtra({{ $extra->id }},1)">
                                                <i class="fas fa-backspace fa-2x"></i>
                                            </x-jet-danger-button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </td>
                <td class="p-2 align-middle bg-transparent border-b">
                    <x-jet-button class="bg-success" data-toggle="tooltip" data-placement="top"
                        id="totalExtra_{{ $sale->id }}" title="Cobrar"
                        onclick="viewDetail({{ $sale->id }},1)">
                        {{ $total }}
                    </x-jet-button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
