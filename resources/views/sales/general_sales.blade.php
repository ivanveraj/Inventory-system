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
                                                        id="amount_{{ $sale->id }}_{{ $extra['product_id'] }}_2"
                                                        data-price="{{ $extra['amount'] }}">
                                                        {{ $extra['amount'] }}
                                                    </span>
                                                    <div class="ml-2">
                                                        <div>
                                                            <button type="button" class="text-success"
                                                                onclick="plusExtra({{ $extra['product_id'] }},{{ $sale->id }},2)"
                                                                id="plus_{{ $extra['product_id'] }}_{{ $sale->id }}_1"
                                                                data-toggle="tooltip" data-placement="top"
                                                                title="Sumar">
                                                                <i class="fas fa-plus"></i>
                                                            </button>
                                                        </div>
                                                        <div>
                                                            <button type="button" class="text-danger"
                                                                onclick="minExtra({{ $extra['product_id'] }},{{ $sale->id }},2)"
                                                                id="min_{{ $extra['product_id'] }}_{{ $sale->id }}_1"
                                                                data-toggle="tooltip" data-placement="top"
                                                                title="Restar">
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
                                                    onclick="deleteExtra({{ $extra['product_id'] }},{{ $sale->id }},2)"
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
