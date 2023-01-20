@if (!is_null($day) && !is_null($historyTables))
        <x-card>
            <h4 class="text-center">Resumen del dia</h4>
            <div class="flex flex-wrap mb-4 ">
                <div class="w-full px-3 mb-6 lg:mb-0 lg:w-1/2 lg:flex-none">
                    <div
                        class="flex h-full flex-col break-words rounded-2xl border-0 border-solid bg-red-800 bg-clip-border p-4">
                        <div class="h-full text-white">
                            <p class="text-center text-2xl">Ventas de productos</p>

                            <div class="alert alert-danger mt-4" role="alert">
                                <p class="text-center text-black font-bold text-xl mb-0 italic">Total Recaudado X Productos:
                                    ${{ formatMoney($currentSales - $currentTValueTable) }}</p>
                            </div>
                            <div class="alert alert-danger mt-4" role="alert">
                                <p class="text-center text-black font-bold text-xl mb-0 italic">Total en caja:
                                    ${{ formatMoney($currentSales) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="w-full px-3 mb-6 lg:mb-0 lg:w-1/2 lg:flex-none">
                    <div
                        class="flex h-full flex-col break-words rounded-2xl border-0 border-solid bg-red-800 bg-clip-border p-4">
                        <div class="flex flex-col flex-auto h-full text-white">
                            <span class="text-center text-2xl mb-1">Recaudado en las mesas</span>
                            <table class="w-full text-white text-center mb-3">
                                <thead class="bg-blue-800">
                                    <tr class="px-6 py-3 font-bold uppercase align-middle whitespace-nowrap text-white">
                                        <th>Mesa</th>
                                        <th>Tiempo</th>
                                        <th>Recaudado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($historyTables as $historyT)
                                        @php
                                            $table = $historyT->Table;
                                        @endphp
                                        <tr>
                                            <td>{{ is_null($table) ? 'Mesa sin nombre' : $table->name }}</td>
                                            <td>{{ $historyT->time }}</td>
                                            <td>${{ formatMoney($historyT->total) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="alert alert-danger mb-0" role="alert">
                                <p class="text-center text-black font-bold text-xl mb-0 italic">Total tiempo:
                                    {{ $currentTTimeTable }} minutos</p>
                                <p class="text-center text-black font-bold text-xl mb-0 italic">Total recaudado:
                                    ${{ formatMoney($currentTValueTable) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </x-card>
    @endif