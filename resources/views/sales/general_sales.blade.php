@if (!$general->isEmpty())
    <table id="general" class="table dt-responsive nowrap w-100">
        <thead class="bg-secondary text-white vertical-align-middle">
            <tr class="text-center">
                <th>Cliente</th>
                <th>Productos</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody class="text-center">
            @foreach ($general as $sale)
                @php
                    /*   $extras = $sale->Extras; */
                    /*  $total = 0; */
                    /*   foreach ($extras as $e) {
                        $total += $e->total;
                    } */
                @endphp
                <tr>
                    <td>
                        <x-jet-input type="text" id="client_{{ $sale->id }}" value="{{ $sale->client }}"
                            class="w-full" onblur="initChangeClient({{ $sale->id }})" placeholder="Escribe un nombre"
                            style="max-width: 280px">
                        </x-jet-input>
                    </td>
                    <td>
                        <div class="flex justify-center">
                            <form id="formAddProduct_{{ $sale->id }}" action="{{ route('sale.addProduct') }}"
                                method="POST">
                                @csrf
                                <input type="hidden" value="{{ $sale->id }}" name="sale_id">
                                <div
                                    class="flex justify-center items-center {{ empty($sale->ArrayExtras) ? '' : 'mb-3' }}">

                                    <x-jet-input type="number" name="amount" style="max-width: 5rem !important"
                                        placeholder="####">
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
                                        <th>Producto</th>
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
                                                <x-jet-input type="number" id="amountExtra_{{ $extra['product_id'] }}"
                                                    value="{{ $extra['amount'] }}" style="width: 80px">
                                                </x-jet-input>
                                            </td>
                                            <td id="priceExtra_{{ $extra['product_id'] }}"
                                                data-price="{{ $extra['price'] }}">
                                                ${{ formatMoney($extra['price']) }}</td>
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
                    <td class="p-2">
                        @dump($sale->total)
                        <button type="submit" data-toggle="tooltip" data-placement="top" title="Cobrar"
                            onclick="viewDetail({{ $sale->id }},2)" id="totalExtra_{{ $sale->id }}"
                            class="btn bg-primary text-white font-extrabold">
                            {{ $sale->total }}
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
