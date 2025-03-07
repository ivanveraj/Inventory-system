@extends('layouts.admin.base')

@section('title', 'Inventario')
@section('title_page', 'Inventario')

@section('breadcrumb')
    <li class="breadcrumb-item">Inventario</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="flex justify-between my-3">
                <div>
                    <button type="button" onclick="reloadTable()" data-toggle="tooltip" data-placement="top" title="Recargar">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
                <x-jet-button type="button" onclick="create()">Nuevo producto</x-jet-button>
            </div>

            <table id="products" class="table dt-responsive nowrap w-100">
                <thead class="bg-secondary text-white vertical-align-middle">
                    <tr class="text-center">
                        <th>Producto</th>
                        <th>Stock</th>
                        <th>Precio</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <div id="contCreate"></div>
    <div id="contEdit"></div>
    <div id="contStock"></div>
@endsection
@push('js')
    <script src="{{ asset('js/admin/sweetalert2.js') }}"></script>
    <script>
        $(function() {
            reloadTable()
        });

        function reloadTable() {
            $('[data-toggle="tooltip"], .tooltip').tooltip("hide");
            $('#products').DataTable().destroy()
            $('#products').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'pageLength', 'excel'
                ],
                "lengthMenu": [25, 50, 100, 200, 400, 600],
                responsive: true,
                processing: true,
                ajax: {
                    url: "{{ route('products.list') }}"
                },
                columns: [{
                    data: 'name',
                    width: '35%'
                }, {
                    data: 'amount',
                    width: '20%'
                }, {
                    data: 'saleprice',
                    width: '20%'
                }, {
                    data: 'state',
                    width: '15%'
                }, {
                    data: 'actions',
                    width: '10%'
                }],
                "aaSorting": [],
                language: lang,
                "drawCallback": function(settings) {
                    $('[data-toggle="tooltip"]').tooltip();
                }
            })
        }

        function create() {
            blockPage();
            $.get(`{{ route('product.create') }}`, function(r) {
                $("#contCreate").html(r);
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
                        success: function(r) {
                            unblockPage();
                            $("#create").modal('hide');
                            addToastr(r.type, r.title, r.message)
                            reloadTable()
                        },
                        error: function(r) {
                            unblockPage();
                            addErrorInputs('#FormCreate', r)
                        }
                    });
                });
            }).fail(function(r) {
                unblockPage();
            });
        }

        function edit(id) {
            blockPage()
            $.get(`{{ route('product.show') }}/${id}`, function(r) {
                if (r.status != undefined) {
                    addToastr(r.type, r.title, r.message)
                } else {
                    $("#contEdit").html(r);
                }
            }).done(function(r) {
                unblockPage()
                if (r.status == undefined) {
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
                            success: function(r) {
                                unblockPage()
                                $("#edit").modal('hide');
                                $('#FormEdit')[0].reset();
                                reloadTable()
                                addToastr(r.type, r.title, r.message)
                            },
                            error: function(r) {
                                unblockPage()
                                addErrorInputs('#FormEdit', r)
                            }
                        });
                    });
                }
            }).fail(function() {
                unblockPage()
            });
        }

        function archive(id, state) {
            let msj = state == 1 ? "Desea archivar el producto" : "Desea activar el producto"
            let sw = SweetConfirmation(msj, "Si", "No")
            sw.then(r => {
                if (r == true) {
                    blockPage();
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('product.archive') }}",
                        data: {
                            "_token": "{{ csrf_token() }}",
                            'id': id
                        },
                        dataType: "json",
                        success: function(r) {
                            unblockPage()
                            addToastr(r.type, r.title, r.message)
                            reloadTable();
                        },
                        error: function(r) {
                            unblockPage()
                        }
                    });
                }
            })
        }

        function addStock(id) {
            blockPage();
            $.get(`{{ route('product.addStock') }}/${id}`, function(r) {
                if (r.status != undefined) {
                    addToastr(r.type, r.title, r.message)
                } else {
                    $("#contStock").html(r);
                }
            }).done(function(r) {
                unblockPage()
                if (r.status === undefined) {
                    $("#addStock").modal('show')

                    $('#addStock').on('shown.bs.modal', function() {
                        reloadAddStockTable();
                    });


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
                            success: function(r) {
                                addToastr(r.type, r.title, r.message);
                                if (r.status === 1) {
                                    $("#addStock").modal('hide');
                                    addStock(id);
                                    reloadTable();
                                } else {
                                    unblockPage();
                                }
                            },
                            error: function(r) {
                                unblockPage();
                                addErrorInputs('#FormAdd', r)
                            }
                        });
                    });
                }
            }).fail(function() {
                unblockPage()
            });
        }

        function reloadAddStockTable() {
            $('[data-toggle="tooltip"], .tooltip').tooltip("hide");
            $('#history_price').DataTable().destroy();
            var table = $('#history_price').DataTable({
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

            table.columns.adjust().responsive.recalc();
        }

        function deleteStock(id) {
            blockPage()
            $.get(`{{ route('product.deleteStock') }}/${id}`, function(r) {
                if (r.status != undefined) {
                    addToastr(r.type, r.title, r.message)
                } else {
                    $("#contStock").html(r);
                }
            }).done(function(r) {
                unblockPage()
                if (r.status == undefined) {
                    $("#deleteStock").modal('show')

                    $('#FormDelete').submit(function(e) {
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
                            success: function(r) {
                                unblockPage()
                                $("#deleteStock").modal('hide');
                                reloadTable()
                                addToastr(r.type, r.title, r.message)
                            },
                            error: function(r) {
                                unblockPage()
                                addErrorInputs('#FormDelete', r)
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
