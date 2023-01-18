<table id="general" class="items-center w-full mb-0 align-top border-gray-200 text-slate-900">
    <thead class="text-center">
        <tr
            class="px-6 py-3 font-bold uppercase align-middle border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">
            <th>Cliente</th>
            <th>Productos</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody class="text-center">
        @foreach ($general as $sale)
            @php
                $extras = $sale->Extras;
                $total = 0;
                foreach ($extras as $e) {
                    $total += $e->total;
                }
            @endphp
            <tr>
                <td class="p-2 align-middle bg-transparent border-b">
                    <x-jet-input type="text" id="client_{{ $sale->id }}" value="{{ $sale->client }}" class="w-full"
                        onblur="initChangeClient({{ $sale->id }})" placeholder="Escribe un nombre"
                        style="max-width: 280px">
                    </x-jet-input>
                </td>
                <td class="p-2 align-middle bg-transparent border-b">
                    <div class="flex justify-center">
                        <form id="formAddProduct_{{ $sale->id }}" action="{{ route('sale.addProduct') }}"
                            method="POST">
                            @csrf
                            <input type="hidden" value="{{ $sale->id }}" name="sale_id">
                            <div class="flex justify-center items-center {{ $extras->isEmpty() ? '' : 'mb-3' }}">
                                <div class="flex justify-center items-center {{ $extras->isEmpty() ? '' : 'mb-3' }}">
                                    <div style="width: 120px">
                                        <x-jet-input type="number" name="amount" class="w-full"
                                            placeholder="Cantidad">
                                        </x-jet-input>
                                    </div>
                                    <div class="flex items-center ml-3 w-full">
                                        <select id="selectProduct_{{ $sale->id }}" name="product_id"
                                            class="form-control"></select>

                                        <button type="submit" class="btn btn-secondary btn-sm ml-2"
                                            data-toggle="tooltip" data-placement="top" title="Agregar producto">
                                            <i class="fa fa-plus"></i>
                                        </button>
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
                                        <td class="break-words p-2 align-middle bg-transparent border-b">
                                            {{ $extra->Product->name }}</td>
                                        <td class="p-2 align-middle bg-transparent border-b">
                                            <div style="width: 100px">
                                                <x-jet-input type="number" id="amountExtra_{{ $extra->id }}"
                                                    class="w-full" value="{{ $extra->amount }}">
                                                </x-jet-input>
                                            </div>
                                        </td>
                                        <td class="p-2 align-middle bg-transparent border-b"
                                            id="priceExtra_{{ $extra->id }}" data-price="{{ $extra->price }}">
                                            {{ $extra->price }}</td>
                                        <td class="p-2 align-middle bg-transparent border-b">
                                            <button type="button" class="btn btn-sm bg-danger text-white"
                                                onclick="deleteExtra({{ $extra->id }},2)" data-toggle="tooltip"
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
                            onclick="viewDetail({{ $sale->id }},2)">
                            {{ $total }}
                        </x-jet-button>
                    @else
                        <span class="btn bg-success text-white">0</span>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
