@extends('layouts.admin.base')

@section('title', 'Historial de ventas')
@section('title_page', 'Historial de ventas')

@section('breadcrumb')
    <li class="breadcrumb-item">Historial de ventas</li>
@endsection

@push('css')
    <link href="{{ asset('css/admin/select2.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('content')
    <div class="card">
        <div class="card-body">
            <div
                class="flex h-full flex-col break-words rounded-2xl border-0 border-solid bg-gray-100 bg-clip-border p-2 mb-3">
                <div class="h-full">
                    <div>
                        @foreach ($historySales as $historyT)
                            @php
                                $table = $historyT->Table;
                            @endphp
                            <div class="flex justify-between items-center w-full p-2" style="border-bottom: solid 1px gray">
                                <div class="w-2/5">
                                    <p class="mb-0">{{ $historyT->client }}</p>
                                    <p class="italic mb-0 text-xs">Fecha: {{ $historyT->created_at }}</p>
                                </div>
                                <div class="row w-2/5">
                                    <div class="col-sm-6 text-center" style="padding: 0 !important">
                                        <div><small>Tiempo</small></div>
                                        <span>{{ $historyT->time }}</span>
                                    </div>
                                    <div class="col-sm-6 text-center" style="padding: 0 !important">
                                        <div><small>Total</small></div>
                                        <span>${{ formatMoney($historyT->total) }}</span>
                                    </div>
                                </div>
                                <div class="w-1/5 flex justify-center items-end">
                                    <x-jet-button class="bg-info" data-toggle="tooltip" data-placement="top" type="button"
                                        id="totalExtra" title="Detalle" onclick="viewDetail({{ $historyT->id }})">
                                        <i class="fas fa-clipboard-list fa-2x"></i>
                                    </x-jet-button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="contModal"></div>
@endsection
@push('js')
    <script src="{{ asset('js/admin/sweetalert2.js') }}"></script>
    <script src="{{ asset('js/admin/select2.min.js') }}"></script>
    <script>
        function viewDetail(history_id) {
            $.get(`{{ route('sale.histoyDetail') }}/${history_id}`, function(r) {
                $("#contModal").html(r);
            }).done(function() {
                unblockPage();

                $("#modalDetail").modal('show');

                $('#modalDetail').on('shown.bs.modal', function() {
                    $('#detailTable').DataTable().destroy()
                    var table = $('#detailTable').DataTable({
                        responsive: true,
                        searching: false,
                        lengthChange: false,
                        bInfo: false,
                        columnDefs: [{
                            width: "18%"
                        }, {
                            width: "64%"
                        }, {
                            width: "18%"
                        }]
                    })
                    table.columns.adjust().responsive.recalc();
                });

            }).fail(function(r) {
                unblockPage();
                console.log(r);
            });
        }
    </script>
@endpush
