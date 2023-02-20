@extends('layouts.admin.base')

@section('title', 'Dashboard')
@section('title_page', 'Dashboard')


@section('content')
    @if (!is_null($day) && !is_null($historyTables))
        <div class="card">
            <div class="card-header bg-white">
                <span class="text-base">Resumen del dia</span>
            </div>
            <div class="card-body">
                <div class="alert bg-secondary text-white" role="alert">
                    <p class="text-center font-bold text-xl mb-0 italic">
                        Total en caja: ${{ formatMoney($currentSales) }}
                    </p>
                </div>
                <div class="flex w-full gap-2">
                    <div class="alert bg-success text-white w-full" role="alert">
                        <p class="text-center font-bold text-xl mb-0 italic">
                            Total Recaudado X Productos: ${{ formatMoney($currentSales - $currentTValueTable) }}
                        </p>
                    </div>
                    <div class="alert bg-info text-white w-full" role="alert">
                        <p class="text-center font-bold text-xl mb-0 italic">
                            Total Recaudado X Tiempo: ${{ formatMoney($currentTValueTable) }}
                        </p>
                    </div>
                </div>
                <div class="flex justify-center ">
                    <div class="w-full px-3 mb-6 lg:mb-0 lg:flex-none">
                        <div
                            class="flex bg-secondary h-full flex-col break-words rounded-2xl border-0 border-solid bg-clip-border p-4">
                            <div class="flex flex-col flex-auto h-full text-white">
                                <p class="text-center font-bold text-xl mb-1 italic">
                                    Recaudado en las mesas
                                </p>
                                <table class="w-full text-white text-center mb-3">
                                    <thead class="bg-info">
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
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if (!is_null($lastDay) && !is_null($lastHistoryTables))
        <div class="card">
            <div class="card-header bg-white text-black">
                <span class="text-base">Resumen del ultimo dia</span>
            </div>
            <div class="card-body">
                <div class="alert bg-warning text-black" role="alert">
                    <p class="text-center font-bold text-xl mb-0 italic">
                        Total en caja: ${{ formatMoney($lastSales) }}
                    </p>
                </div>
                <div class="flex w-full gap-2">
                    <div class="alert bg-success text-white w-full" role="alert">
                        <p class="text-center font-bold text-xl mb-0 italic">
                            Total Recaudado X Productos: ${{ formatMoney($lastSales - $lastTValueTable) }}
                        </p>
                    </div>
                    <div class="alert bg-info text-white w-full" role="alert">
                        <p class="text-center font-bold text-xl mb-0 italic">
                            Total Recaudado X Tiempo: ${{ formatMoney($lastTValueTable) }}
                        </p>
                    </div>
                </div>
                <div class="flex justify-center">
                    <div class="w-full px-3 mb-6 lg:mb-0 lg:flex-none">
                        <div
                            class="flex bg-warning text-black h-full flex-col break-words rounded-2xl border-0 border-solid bg-clip-border p-4">
                            <div class="flex flex-col flex-auto h-full">
                                <p class="text-center font-bold text-xl mb-1 italic">
                                    Recaudado en las mesas
                                </p>
                                <table class="w-full text-black text-center mb-3">
                                    <thead class="bg-info">
                                        <tr class="px-6 py-3 font-bold uppercase align-middle whitespace-nowrap text-black">
                                            <th>Mesa</th>
                                            <th>Tiempo</th>
                                            <th>Recaudado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
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
                                <div class="alert alert-danger mb-0" role="alert">
                                    <p class="text-center text-black font-bold text-xl mb-0 italic">
                                        Total tiempo: {{ $lastTTimeTable }} minutos</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="row">




        <!-- end col -->
    </div>

    <div class="card">
        <div class="card-header bg-white text-black">
            <span class="text-base">Ventas de los ultimos 7 dias</span>
        </div>
        <div class="card-body">
            @foreach ($lastFourDay as $day)
                <div class="w-full max-w-full px-3 mb-6 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4">
                    <div
                        class="relative flex flex-col min-w-0 break-words bg-secondary shadow-soft-xl rounded-2xl bg-clip-border">
                        <div class="flex-auto p-4">
                            <div class="flex flex-row -mx-3">
                                <div class="w-full text-center rounded-lg">
                                    <div class="">
                                        <p class="mb-0 font-sans font-semibold leading-normal text-sm text-white">
                                            {{ date('H:i d-M', strtotime($day->created_at)) . ' - ' . date('H:i d-M', strtotime($day->finish_day)) }}
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

    {{-- <div class="card">
        <div class="card-header bg-white text-black">
            <span class="text-base">Graficas</span>
        </div>
        <div class="card-body">
            <div class="mx-8" style="max-width: 500px; max-height:500px">
                <div>
                    <canvas id="myChart" height="340" class="mb-4 w-full"></canvas>
                </div>
            </div>
        </div>
    </div> --}}
@endsection
@push('js')
    <script>
        $(function() {
            /* initChart(); */
        })

        function initChart() {
            var options = {
                chart: {
                    type: "area",
                    height: 350,
                    foreColor: '#404040'
                },
                title: {
                    text: "{{ __('tecnico.graf_ganancias_mes_actual') }}",
                    align: 'left'
                },
                series: [],
                colors: ["#536d64"],
                stroke: {
                    curve: 'straight'
                },
                grid: {
                    borderColor: "#404040"
                },
                noData: {
                    text: "{{ __('tecnico.cargando') }}"
                }
            };

            var chart = new ApexCharts(document.querySelector("#chart"), options);
            chart.render();

            /*   $.getJSON("", function(r) {
                  console.log(r);
                  if (r.status === 1) {
                      chart.updateSeries([{
                          data: r.data
                      }]);
                  } else {
                      addToastr(r.type, r.title, r.message);
                  }
              }); */
        }
    </script>
@endpush
