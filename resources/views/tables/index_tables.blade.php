@extends('layouts.admin.base')

@section('title', 'Gestión de mesas')
@section('title_page', 'Gestión de mesas')

@section('breadcrumb')
    <li class="text-size-sm pl-2 capitalize leading-normal text-slate-700 before:float-left before:pr-2 before:text-gray-600 before:content-['/']"
        aria-current="page">Gestión de mesas</li>
@endsection

@section('content')
    <x-card>
        <div class="flex justify-between my-3 p-2">
            <div>
                <button type="button" onclick="reloadTable()" data-toggle="tooltip" data-placement="top" title="Recargar">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
            <x-jet-button type="button" onclick="create()">Crear un nuevo producto</x-jet-button>
        </div>
        <div class="flex justify-center">
            <table id="tables" class="p-4 items-center w-full align-top border-gray-200 text-slate-500 text-center">
                <thead>
                    <tr
                        class="px-6 py-3 font-bold uppercase align-middle border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">
                        <th>Mesa</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </x-card>

    <div id="contCreate"></div>
    <div id="contEdit"></div>
@endsection
@push('js')
    <script src="{{ asset('js/admin/sweetalert2.js') }}"></script>
    <script>
        var state = document.getElementById('allTables');
        var active = 0;
        $(function() {
            reloadTable()
        });

        state.addEventListener('change', function() {
            active = (active == 0) ? 1 : 0
            reloadTable()
        })


        function reloadTable() {
            $('#tables').DataTable().destroy()
            $('#tables').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('tables.list') }}" + `?active=${active}`
                },
                columns: [{
                    data: 'name',
                    width: '40%'
                }, {
                    data: 'state',
                    width: '30%'
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
            $.get(`{{ route('table.create') }}`, function(response) {
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
            $.get(`{{ route('table.show') }}/${id}`, function(response) {
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
            let msj = state == 1 ? "Desea archivar la mesa" : "Desea activar la mesa"
            let sw = SweetConfirmation(msj, "Si", "No")
            sw.then(response => {
                if (response == true) {
                    blockPage();
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('table.archive') }}",
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
                            console.log(response);
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
