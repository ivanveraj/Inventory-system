@extends('layouts.admin.base')

@section('title', 'Inventario')
@section('title_page', 'Inventario')

@section('breadcrumb')
    <li class="text-size-sm pl-2 capitalize leading-normal text-slate-700 before:float-left before:pr-2 before:text-gray-600 before:content-['/']"
        aria-current="page">Inventario</li>
@endsection

@section('content')
    <x-card>
        <div class="flex justify-between my-3 p-2">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="allProducts" />
                <label class="form-check-label" for="allProducts">Listar todos los productos</label>
            </div>
            <x-jet-button type="button" onclick="create()">Crear un nuevo producto</x-jet-button>
        </div>
        <div class="flex justify-center">
            <table id="products" class="p-4 items-center align-top border-gray-200 text-slate-500 text-center" style="width: 100%">
                <thead>
                    <tr
                        class="px-6 py-3 font-bold uppercase align-middle border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">
                        <th>Producto</th>
                        <th>Stock</th>
                        <th>Compra</th>
                        <th>Venta</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </x-card>

    <div id="contCreate"></div>
    <div id="contEdit"></div>
    <div id="contStock"></div>
@endsection
@push('js')
    <script src="{{ asset('js/admin/sweetalert2.js') }}"></script>
    <script>
        var state = document.getElementById('allProducts');
        var active = 0;
        $(function() {
            reloadTable()
        });

        state.addEventListener('change', function() {
            active = (active == 0) ? 1 : 0
            reloadTable()
        })


        function reloadTable() {
            $('#products').DataTable().destroy()
            $('#products').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('products.list') }}" + `?active=${active}`
                },
                columns: [{
                    data: 'name',
                    width: '30%'
                }, {
                    data: 'amount',
                    width: '15%'
                }, {
                    data: 'buyprice',
                    width: '15%'
                }, {
                    data: 'saleprice',
                    width: '15%'
                }, {
                    data: 'state',
                    width: '15%'
                }, {
                    data: 'actions',
                    width: '30%'
                }],
                "drawCallback": function(settings) {
                    $('[data-toggle="tooltip"]').tooltip();
                }
            })
        }

        function create() {
            blockPage();
            $.get(`{{ route('product.create') }}`, function(response) {
                $("#contCreate").html(response);
            }).done(function() {
                unblockPage();
                $("#create").modal('show')

                $('#FormCreate').submit(function(e) {
                    e.preventDefault();
                    blockPage();
                    let formData = new FormData(this);
                    let formAction = $(this).attr("action");
                    $.ajax({
                        type: 'POST',
                        url: formAction,
                        data: formData,
                        contentType: false,
                        processData: false,
                        dataType: "json",
                        success: function(response) {
                            unblockPage();
                            $("#create").modal('hide');
                            addToastr(response.type, response.title, response.message)
                            reloadTable()
                        },
                        error: function(response) {
                            unblockPage();
                            addErrorInputs('#FormCreate', response)
                        }
                    });
                });
            }).fail(function(response) {
                unblockPage();
            });
        }

        function edit(id) {
            blockPage()
            $.get(`{{ route('product.show') }}/${id}`, function(response) {
                if (response.status != undefined) {
                    addToastr(response.type, response.title, response.message)
                } else {
                    $("#contEdit").html(response);
                }
            }).done(function(response) {
                unblockPage()
                if (response.status == undefined) {
                    $("#edit").modal('show')

                    $('#FormEdit').submit(function(e) {
                        e.preventDefault();
                        blockPage();
                        let formData = new FormData(this);
                        let formAction = $(this).attr("action");
                        $.ajax({
                            type: 'POST',
                            url: formAction,
                            data: formData,
                            contentType: false,
                            processData: false,
                            dataType: "json",
                            success: function(response) {
                                unblockPage()
                                $("#edit").modal('hide');
                                $('#FormEdit')[0].reset();
                                reloadTable()
                                addToastr(response.type, response.title, response.message)
                            },
                            error: function(response) {
                                unblockPage()
                                addErrorInputs('#FormEdit', response)
                            }
                        });
                    });
                }
            }).fail(function() {
                unblockPage()
            });
        }

        function archive(id,state) {
            let msj = state == 1 ? "Desea archivar el producto" : "Desea activar el producto"
            let sw = SweetConfirmation(msj, "Si", "No")
            sw.then(response => {
                if (response == true) {
                    blockPage();
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('product.archive') }}",
                        data: {
                            "_token": "{{ csrf_token() }}",
                            'id': id
                        },
                        dataType: "json",
                        success: function(response) {
                            unblockPage()
                            addToastr(response.type, response.title, response.message)
                            reloadTable();
                        },
                        error: function(response) {
                            unblockPage()
                        }
                    });
                }
            })
        }

        function addStock(id) {
            blockPage()
            $.get(`{{ route('product.addStock') }}/${id}`, function(response) {
                if (response.status != undefined) {
                    addToastr(response.type, response.title, response.message)
                } else {
                    $("#contStock").html(response);
                }
            }).done(function(response) {
                unblockPage()
                if (response.status == undefined) {
                    $("#addStock").modal('show')

                    $('#FormAdd').submit(function(e) {
                        e.preventDefault();
                        blockPage();
                        let formData = new FormData(this);
                        let formAction = $(this).attr("action");
                        $.ajax({
                            type: 'POST',
                            url: formAction,
                            data: formData,
                            contentType: false,
                            processData: false,
                            dataType: "json",
                            success: function(response) {
                                unblockPage()
                                $("#addStock").modal('hide');
                                $('#FormAdd')[0].reset();
                                reloadTable()
                                addToastr(response.type, response.title, response.message)
                            },
                            error: function(response) {
                                unblockPage()
                                addErrorInputs('#FormAdd', response)
                            }
                        });
                    });
                }
            }).fail(function() {
                unblockPage()
            });
        }
    </script>
@endpush
