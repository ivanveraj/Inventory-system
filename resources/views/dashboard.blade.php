@extends('layouts.admin.base')

@section('title', 'Dashboard')
@section('title_page', 'Dashboard')


@section('content')
    <x-card>
        <div>
            @if ($total != 0)
                <h3 class="text-center">Ventas totales del dia: {{ formatMoney($total) }}</h3>
            @endif
        </div>
        <div>
            <h4 class="text-center">Ventas de los ultimos 7 dias</h4>
            <div class="flex flex-wrap -mx-3 justify-center">
                @foreach ($lastFourDay as $day)
                    <div class="w-full max-w-full px-3 mb-6 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4">
                        <div
                            class="relative flex flex-col min-w-0 break-words bg-gray-200 shadow-soft-xl rounded-2xl bg-clip-border">
                            <div class="flex-auto p-4">
                                <div class="flex flex-row -mx-3">
                                    <div class="w-full text-center rounded-lg bg-gradient-to-tl from-purple-700 to-pink-500">
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
            initChart()
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
