@extends('layouts.admin.base')

@section('title', 'Dashboard')
@section('title_page', 'Dashboard')


@section('content')
    @if (!is_null($day) && !is_null($historyTables))
        <x-card>
            <h4 class="text-center">Resumen del dia</h4>
            <div class="flex flex-wrap mb-4 ">
                <div class="w-full px-3 mb-6 lg:mb-0 lg:w-1/2 lg:flex-none">
                    <div
                        class="flex h-full flex-col break-words rounded-2xl border-0 border-solid bg-red-800 bg-clip-border p-4">
                        <div class="h-full text-white">
                            <p class="text-center text-2xl font-extrabold">Ventas de productos</p>

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
                            <span class="text-center text-2xl mb-1 font-extrabold">Recaudado en las mesas</span>
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

    @if (!is_null($lastDay) && !is_null($lastHistoryTables))
        <x-card>
            <h4 class="text-center">Resumen del ultimo dia</h4>
            <div class="flex flex-wrap mb-4 ">
                <div class="w-full px-3 mb-6 lg:mb-0 lg:w-1/2 lg:flex-none">
                    <div class="flex h-full flex-col break-words rounded-2xl border-0 border-solid bg-clip-border p-4"
                        style="background-color: #ffb000">
                        <div class="h-full text-black">
                            <p class="text-center text-2xl font-extrabold">Ventas de productos</p>

                            <div class="alert bg-black mt-4" role="alert">
                                <p class="text-center text-white font-bold text-xl mb-0 italic">Total Recaudado X Productos:
                                    ${{ formatMoney($lastSales - $lastTValueTable) }}</p>
                            </div>
                            <div class="alert bg-black mt-4" role="alert">
                                <p class="text-center text-white font-bold text-xl mb-0 italic">Total en caja:
                                    ${{ formatMoney($lastSales) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="w-full px-3 mb-6 lg:mb-0 lg:w-1/2 lg:flex-none">
                    <div class="flex h-full flex-col break-words rounded-2xl border-0 border-solid bg-clip-border p-4"
                        style="background-color: #ffb000">
                        <div class="flex flex-col flex-auto h-full text-black">
                            <span class="text-center text-2xl mb-1 font-extrabold">Recaudado en las mesas</span>
                            <table class="w-full text-white text-center mb-3">
                                <thead style="background-color: #021f6d">
                                    <tr class="px-6 py-3 font-bold uppercase align-middle whitespace-nowrap text-white">
                                        <th>Mesa</th>
                                        <th>Tiempo</th>
                                        <th>Recaudado</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-slate-800">
                                    @foreach ($lastHistoryTables as $historyT)
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
                            <div class="alert bg-black mb-0" role="alert">
                                <p class="text-center text-white font-bold text-xl mb-0 italic">Total tiempo:
                                    {{ $lastTTimeTable }} minutos</p>
                                <p class="text-center text-white font-bold text-xl mb-0 italic">Total recaudado:
                                    ${{ formatMoney($lastTValueTable) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </x-card>
    @endif

    <x-card>
        <div>
            <h4 class="text-center">Ventas de los ultimos 7 dias</h4>
            <div class="flex flex-wrap -mx-3 justify-center">
                @foreach ($lastFourDay as $day)
                    <div class="w-full max-w-full px-3 mb-6 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4">
                        <div
                            class="relative flex flex-col min-w-0 break-words bg-gray-200 shadow-soft-xl rounded-2xl bg-clip-border">
                            <div class="flex-auto p-4">
                                <div class="flex flex-row -mx-3">
                                    <div
                                        class="w-full text-center rounded-lg bg-gradient-to-tl from-purple-700 to-pink-500">
                                        <div class="">
                                            <p class="mb-0 font-sans font-semibold leading-normal text-sm text-white">
                                                {{ date('d-M', strtotime($day->created_at)) . ' - ' . date('d M', strtotime($day->finish_day)) }}
                                            </p>
                                            <h5 class="mb-0 font-bold text-white">
                                                {{ '$' . formatMoney($day->total) }}
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </x-card>
    <x-card>
        <div class="mx-8" style="max-width: 500px; max-height:500px">
            <div>
                <canvas id="myChart" height="340" class="mb-4 w-full"></canvas>
            </div>
        </div>
    </x-card>
@endsection
@push('js')
    <script src="{{ asset('js/admin/chart.min.js') }}"></script>
    <script>
        $(function() {
            initChart();
        })


        function initChart() {
            $.ajax({
                type: "GET",
                dataType: "json",
                url: "{{ route('getDataSales') }}",
                success: function(r) {
                    let days = r.data.days;
                    let sales = r.data.sales;

                    const data = {
                        labels: days,
                        datasets: [{
                            label: 'Dias',
                            data: sales,
                            backgroundColor: [
                                'rgb(255, 99, 132)',
                                'rgb(54, 162, 235)',
                                'rgb(255, 205, 86)',
                                'rgb(75, 192, 192)',
                                'rgb(201, 203, 207)',
                                'rgb(54, 162, 235)'
                            ],
                            hoverOffset: 4
                        }]
                    };

                    const ctx = document.getElementById('myChart').getContext('2d');
                    const myChart = new Chart(ctx, {
                        type: 'doughnut',
                        data: data,
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'top',
                                },
                                title: {
                                    display: true,
                                    text: 'Ventas de la ultima semana'
                                }
                            }
                        }
                    });
                }
            });
        }
    </script>
@endpush
