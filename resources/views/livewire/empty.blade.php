<div class="w-full">
    <div class="flex justify-end" style="padding-top:.5rem">
        {{-- {{ $this->addExtraAction }} --}}
    </div>

    {{-- @if (!$extras)
        <table class="w-full align-top border-gray-700 text-gray-300 text-sm my-4">
            <thead class="text-left bg-gray-800">
                <tr class="px-4 py-2 font-semibold uppercase border-y border-gray-700 text-sm text-white">
                    <th class="p-1">Producto</th>
                    <th class="p-1 text-center">Cantidad</th>
                    <th class="p-1 text-center">Precio</th>
                    <th class="p-1"></th>
                </tr>
            </thead>

            <tbody>
                @foreach ($extras as $extra)
                    <tr class="border-b border-gray-700 hover:bg-gray-800 transition">
                        <td class="p-1 break-words">{{ $extra->Product->name }}</td>
                        <td class="p-1 flex justify-center">
                            <x-filament::input.wrapper class="input-wrapper-fit">
                                <x-filament::input type="number" min="1"
                                    oninput="this.value = Math.max(1, this.value)"
                                    wire:change="updateExtra({{ $extra->id }}, $event.target.value)"
                                    value="{{ $extra->amount }}" class="w-fit max-w-32" />
                            </x-filament::input.wrapper>
                        </td>
                        <td class="p-1 text-center" id="priceExtra_{{ $extra->id }}"
                            data-price="{{ $extra->price }}">
                            {{ formatMoney($extra->price) }}
                        </td>
                        <td class="p-1 text-center">
                            {{--  {{ ($this->deleteExtraAction)(['extraId' => $extra->id]) }} --}}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif --}}

</div>
