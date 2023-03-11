<table id="sales" class="table dt-responsive nowrap w-100">
    <thead class="bg-secondary text-white vertical-align-middle">
        <tr class="text-center">
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
                
                $sale->total += $total;
                
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
            <tr>
                <td>
                    <span class="text-base font-medium ">{{ is_null($table) ? 'Tabla sin nombre' : $table->name }}</span>
                </td>
                <td>
                    <div>
                        <div class="justify-center {{ is_null($sale->start_time) ? 'hidden' : 'd-flex' }}"
                            id="timer_{{ $sale->id }}">
                            <div id="hours_{{ $sale->id }}" data-time="{{ $hours }}">{{ $labelH }}
                            </div>:
                            <div id="minutes_{{ $sale->id }}" data-time="{{ $minutes }}">
                                {{ $labelM }}
                            </div>:
                            <div id="seconds_{{ $sale->id }}" data-time="{{ $seconds }}">
                                {{ $labelS }}
                            </div>
                        </div>
                        <button type="button" onclick="startTime({{ $sale->id }})"
                            onclick="startTime({{ $sale->id }})" data-toggle="tooltip" data-placement="top"
                            title="Iniciar tiempo"
                            class="btn bg-primary text-white startTime_{{ $sale->id }} {{ is_null($sale->start_time) ? '' : 'd-none' }}">
                            <i class="fas fa-play"></i>
                        </button>
                    </div>
                </td>
                <td>
                    <div class="flex justify-center w-100">
                        <form id="formAddProduct_{{ $sale->id }}" action="{{ route('sale.addProduct') }}"
                            method="POST">
                            @csrf
                            <input type="hidden" value="{{ $sale->id }}" name="sale_id">
                            <div
                                class="flex justify-center items-center {{ empty($sale->ArrayExtras) ? '' : 'mb-3' }}">
                                <x-jet-input type="number" name="amount" style="max-width: 5rem !important"
                                    placeholder="Cant">
                                </x-jet-input>
                                <div class="flex items-center ml-3 w-full">
                                    <select id="selectProduct_{{ $sale->id }}" name="product_id"
                                        class="form-control jquerySelect2-tag"></select>
                                    <button type="submit" class="btn bg-primary text-white btn-sm ml-2"
                                        data-toggle="tooltip" data-placement="top" title="Agregar producto">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    @if (!empty($sale->ArrayExtras))
                        <table class="w-100">
                            <thead class="vertical-align-middle">
                                <tr class="text-center">
                                    <th class="w-50">Producto</th>
                                    <th>Cantidad</th>
                                    <th>Precio</th>
                                    <th>Accion</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sale->ArrayExtras as $extra)
                                    <tr>
                                        <td class="break-words text-xs">{{ $extra['name'] }}</td>
                                        <td>
                                            <div class="flex justify-center items-center">
                                                <span class="text-base font-semibold"
                                                    id="amount_{{ $sale->id }}_{{ $extra['product_id'] }}_1"
                                                    data-price="{{ $extra['amount'] }}">
                                                    {{ $extra['amount'] }}
                                                </span>
                                                <div class="ml-2">
                                                    <div>
                                                        <button type="button" class="text-success"
                                                            onclick="plusExtra({{ $extra['product_id'] }},{{ $sale->id }},1)"
                                                            id="plus_{{ $extra['product_id'] }}_{{ $sale->id }}_1"
                                                            data-toggle="tooltip" data-placement="top" title="Sumar">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </div>
                                                    <div>
                                                        <button type="button" class="text-danger"
                                                            onclick="minExtra({{ $extra['product_id'] }},{{ $sale->id }},1)"
                                                            id="min_{{ $extra['product_id'] }}_{{ $sale->id }}_1"
                                                            data-toggle="tooltip" data-placement="top" title="Restar">
                                                            <i class="fas fa-minus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td id="priceExtra_{{ $extra['product_id'] }}"
                                            data-price="{{ $extra['price'] }}">
                                            <span class="text-base font-semibold">
                                                ${{ formatMoney($extra['price']) }}
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm bg-danger text-white"
                                                onclick="deleteExtra({{ $extra['product_id'] }},{{ $sale->id }},1)"
                                                data-toggle="tooltip" data-placement="top" title="Eliminar">
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

                    @if ($sale->total != 0)
                        <button type="submit" data-toggle="tooltip" data-placement="top" title="Cobrar"
                            onclick="viewDetail({{ $sale->id }},1)" id="totalExtra_{{ $sale->id }}"
                            class="btn bg-primary text-white font-extrabold">
                            {{ $sale->total }}
                        </button>
                    @else
                        <span class="btn bg-primary text-white font-extrabold">0</span>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
