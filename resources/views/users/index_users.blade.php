@extends('layouts.admin.base')

@section('title', 'Listado de usuarios')
@section('title_page', 'Listado de usuarios')

@section('breadcrumb')
    <li class="breadcrumb-item">Listado de usuarios</li>
@endsection

@push('css')
    <link href="{{ asset('css/admin/select2.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@php
    $LogUser = Auth::user();
@endphp
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="flex justify-between my-3">
                <div>
                    <button type="button" onclick="reloadTable()" data-toggle="tooltip" data-placement="top" title="Recargar">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
                <div>
                    <x-jet-button type="button" onclick="create()">Crear un nuevo usuario</x-jet-button>
                </div>
            </div>
            <table id="Table" class="table dt-responsive nowrap w-100">
                <thead class="bg-secondary text-white vertical-align-middle">
                    <tr class="text-center">
                        <th class="py-3">Nombre</th>
                        <th class="py-3">Rol</th>
                        <th class="py-3">Estado</th>
                        <th class="py-3">Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <div id="contCreate"></div>
    <div id="contEdit"></div>
    <div id="assignRol"></div>
@endsection
@push('js')
    <script src="{{ asset('js/admin/sweetalert2.js') }}"></script>
    <script src="{{ asset('js/admin/select2.min.js') }}"></script>
    <script>
        $(function() {
            reloadTable()
        });

        function reloadTable() {
            $('[data-toggle="tooltip"], .tooltip').tooltip("hide");
            $('#Table').DataTable().destroy()
            $('#Table').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'pageLength', 'excel'
                ],
                "lengthMenu": [25, 50, 100, 200, 400, 600],
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('users.list') }}"
                },
                columns: [{
                    data: 'name',
                    width: '25%'
                }, {
                    data: 'rol',
                    width: '25%'
                }, {
                    data: 'state',
                    width: '25%'
                }, {
                    data: 'actions',
                    width: '25%'
                }],
                language: lang,
                "drawCallback": function(settings) {
                    $('[data-toggle="tooltip"]').tooltip();
                }
            });
        }

        function create() {
            blockPage();
            $.get(`{{ route('user.create') }}`, function(response) {
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
                console.log(response);
            });
        }

        function edit(user_id) {
            blockPage();
            $.get(`{{ route('user.show') }}/${user_id}`, function(response) {
                $("#contEdit").html(response);
            }).done(function() {
                unblockPage();

                $("#editM").modal('show')

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
                            unblockPage();
                            $("#editM").modal('hide');
                            addToastr(response.type, response.title, response.message)
                            reloadTable()
                        },
                        error: function(response) {
                            unblockPage();
                            console.log(response)
                            addErrorInputs('#FormEdit', response)
                        }
                    });
                });
            }).fail(function(response) {
                unblockPage();
                console.log(response);
            });
        }

        function archive(user_id, state) {
            let msj = state == 1 ? "Desea archivar el usuario" : "Desea activar el usuario"
            let sw = SweetConfirmation(msj, "Si", "No")
            sw.then(response => {
                if (response == true) {
                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        url: '{{ route('user.archive') }}',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            'id': user_id,
                        },
                        success: function(response) {
                            addToastr(response.type, response.title, response.message)
                            reloadTable()
                        },
                        error: function(response) {
                            console.log(response);
                        }
                    });
                }
            })
        }

        function assignRol(id_user) {
            blockPage();
            $.get(`{{ route('user.assign_rol') }}/${id_user}`, function(response) {
                $("#assignRol").html(response);
            }).done(function() {
                unblockPage();
                $('#rol_user').select2({
                    dropdownParent: $("#assignRolM"),
                    width: '100%'
                });
                $("#assignRolM").modal('show')

                $('#FormAssignRol').submit(function(e) {
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
                            $("#assignRolM").modal('hide');
                            addToastr(response.type, response.title, response.message)
                            reloadTable()
                        },
                        error: function(response) {
                            unblockPage();
                            console.log(response)
                            addErrorInputs('#FormAssignRol', response)
                        }
                    });
                });
            }).fail(function(response) {
                unblockPage();
                console.log(response);
            });
        }
    </script>
@endpush
