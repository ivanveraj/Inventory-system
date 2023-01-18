<table id="sales" class="items-center w-full mb-0 align-top border-gray-300 text-slate-900">
    <thead class="text-center">
        <tr style="border-bottom: solid 1px gray"
            class="px-6 py-3 font-bold uppercase align-middle border-b border-gray-300 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">
            <th class="px-2 py-3">Mesa</th>
            <th class="px-2 py-3">Tiempo</th>
            <th class="px-2 py-3">Extras</th>
            <th class="px-2 py-3">Total</th>
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
                
                $labelH = '00';
                $labelM = '00';
                $labelS = '00';
                $hours = 0;
                $minutes = 0;
                $seconds = 0;
                $x = 0;
                if (!is_null($sale->start_time)) {
                    $x = DateDifferenceSeconds(date('Y/m/d H:i:s'), $sale->start_time);
                    $hours = floor($x / 3600);
                    $labelH = $hours < 10 ? '0' . $hours : $hours;
                    $minutes = floor(($x - $hours * 3600) / 60);
                    $labelM = $minutes < 10 ? '0' . $minutes : $minutes;
                    $seconds = $x - $hours * 3600 - $minutes * 60;
                    $labelS = $seconds < 10 ? '0' . $seconds : $seconds;
                }
            @endphp
            <tr class="">
                <td class="p-2 align-middle bg-transparent border-b">
                    {{ is_null($table) ? 'Tabla sin nombre' : $table->name }}</td>
                <td class="p-2 align-middle bg-transparent border-b">
                    <div>
                        <div class="justify-center {{ is_null($sale->start_time) ? 'hidden' : 'd-flex' }}"
                            id="timer_{{ $sale->id }}">
                            <div id="hours_{{ $sale->id }}" data-time="{{ $hours }}">{{ $labelH }}
                            </div>
                            :
                            <div id="minutes_{{ $sale->id }}" data-time="{{ $minutes }}">
                                {{ $labelM }}
                            </div>:
                            <div id="seconds_{{ $sale->id }}" data-time="{{ $seconds }}">
                                {{ $labelS }}
                            </div>
                        </div>
                        <x-jet-button type="button" onclick="startTime({{ $sale->id }})"
                            class="startTime_{{ $sale->id }} {{ is_null($sale->start_time) ? '' : 'hidden' }}"
                            data-toggle="tooltip" data-placement="top" title="Iniciar tiempo">
                            <i class="fas fa-play"></i>
                        </x-jet-button>
                    </div>
                </td>
                <td class="p-2 align-middle bg-transparent border-b">
                    <div class="flex justify-center w-full">
                        <form id="formAddProduct_{{ $sale->id }}" action="{{ route('sale.addProduct') }}"
                            method="POST">
                            @csrf
                            <input type="hidden" value="{{ $sale->id }}" name="sale_id">
                            <div class="flex justify-center items-center {{ $extras->isEmpty() ? '' : 'mb-3' }}">
                                <div style="width: 120px">
                                    <x-jet-input type="number" name="amount" class="w-full" placeholder="Cantidad">
                                    </x-jet-input>
                                </div>

                                <div class="flex items-center ml-3 w-full">
                                    <select id="selectProduct_{{ $sale->id }}" name="product_id"
                                        class="form-control"></select>
                                    <button type="submit" class="btn btn-secondary btn-sm ml-2" data-toggle="tooltip"
                                        data-placement="top" title="Agregar producto">
                                        <i class="fa fa-plus"></i>
                                    </button>
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
                                                value="{{ $extra->amount }}" style="width: 80px">
                                            </x-jet-input>
                                        </td>
                                        <td id="priceExtra_{{ $extra->id }}" data-price="{{ $extra->price }}">
                                            {{ formatMoney($extra->price) }}</td>
                                        <td>
                                            <button type="button" class="btn btn-sm bg-danger text-white"
                                                onclick="deleteExtra({{ $extra->id }},1)" data-toggle="tooltip"
                                                data-placement="top" title="Eliminar">
                                                <i class="fas fa-backspace"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </td>
                <td class="p-2 align-middle bg-transparent border-b">
                    @if ($total != 0)
                        <x-jet-button class="bg-success" data-toggle="tooltip" data-placement="top"
                            id="totalExtra_{{ $sale->id }}" title="Cobrar"
                            onclick="viewDetail({{ $sale->id }},1)">
                            {{ formatMoney($total) }}
                        </x-jet-button>
                    @else
                        <span class="btn bg-success text-white">0</span>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
