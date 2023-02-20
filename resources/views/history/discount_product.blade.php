@extends('layouts.admin.base')

@section('title', 'Historial de descuentos')
@section('title_page', 'Historial de descuentos')

@section('breadcrumb')
    <li class="breadcrumb-item">Historial de descuentos</li>
@endsection

@push('css')
    <link href="{{ asset('css/admin/select2.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('content')
    <div class="card">
        <div class="card-body">
            <table id="table" class="table dt-responsive table-responsive nowrap w-100">
                <thead class="bg-secondary text-white vertical-align-middle">
                    <tr class="text-center">
                        <th>Usuario</th>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($inventoryDiscount as $inventoryD)
                        @php
                            $user = $inventoryD->User;
                            $product = $inventoryD->Product;
                        @endphp
                        <tr class="text-center">
                            <td>
                                <div class="flex justify-start">
                                    <div class="mr-3">
                                        <img class="h-8 w-8 rounded-full object-cover borderBox"
                                            src="{{ $user->profile_photo_url }}">
                                    </div>
                                    <div class="flex flex-col justify-center text-left">
                                        <h6 class="mb-0 text-sm">{{ $user->name }}</h6>
                                        <p class="mb-0 text-xs">Usuario: {{ $user->user }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $product->name }}</td>
                            <td>{{ $inventoryD->amount }}</td>
                            <td>{{ date_format($inventoryD->created_at, 'd-M-Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>


    <div id="contModal"></div>
@endsection
@push('js')
    <script>
        $(function() {
            reloadTable();
        });

        function reloadTable() {
            $('[data-toggle="tooltip"], .tooltip').tooltip("hide");
            $('#table').DataTable().destroy();
            $('#table').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'pageLength', 'excel'
                ],
                "lengthMenu": [25, 50, 100, 200, 400, 600],
                responsive: true,
                processing: true,
                language: lang,
                "drawCallback": function(settings) {
                    $('[data-toggle="tooltip"]').tooltip();
                }
            });
        }
    </script>
@endpush
