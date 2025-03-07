@extends('layouts.admin.base')

@section('title', 'Gestion de roles')
@section('title_page', 'Gestion de roles')

@section('breadcrumb')
    <li class="breadcrumb-item">Gestion de roles</li>
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
                    <button type="button" onclick="reloadTable()" data-toggle="tooltip" data-placement="top"
                        title="Recargar">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
                <x-jet-button type="button" onclick="create()">Crear un nuevo rol</x-jet-button>
            </div>

            <table id="Roles" class="table dt-responsive nowrap w-100">
                <thead class="bg-secondary text-white vertical-align-middle">
                    <tr class="text-center">
                        <th>Rol</th>
                        <th>Usuarios asociados</th>
                        <th>Estado</th>
                        <th>Gestion de permisos</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    </div>

    <div id="contCreateRol"></div>
    <div id="contEditRol"></div>
    <div id="managePermission"></div>
@endsection
@push('js')
    <script src="{{ asset('js/admin/sweetalert2.js') }}"></script>
    <script>
        $(function() {
            reloadTable()
        });

        function reloadTable() {
            $('[data-toggle="tooltip"], .tooltip').tooltip("hide");
            $('#Roles').DataTable().destroy()
            $('#Roles').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'pageLength', 'excel'
                ],
                "lengthMenu": [25, 50, 100, 200, 400, 600],
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('roles.datatables') }}"
                },
                columns: [{
                    data: 'name',
                    width: '30%'
                }, {
                    data: 'users_associated',
                    width: '10%'
                }, {
                    data: 'state',
                    width: '10%'
                }, {
                    data: 'permissions',
                    width: '30%'
                }, {
                    data: 'actions',
                    width: '20%'
                }],
                language: lang,
                "drawCallback": function(settings) {
                    $('[data-toggle="tooltip"]').tooltip();
                }
            })
        }

        function create() {
            blockPage();
            $.get(`{{ route('rol.create') }}`, function(response) {
                $("#contCreateRol").html(response);
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

        function editRol(rol_id) {
            blockPage()
            $.get(`{{ route('rol.show') }}/${rol_id}`, function(response) {
                if (response.status != undefined) {
                    addToastr(response.type, response.title, response.message)
                } else {
                    $("#contEditRol").html(response);
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

        function permissionsRol(rol_id) {
            blockPage()
            $.get(`{{ route('rol.managePermissionsRol') }}/${rol_id}`, function(response) {
                if (response.status != undefined) {
                    addToastr(response.type, response.title, response.message)
                } else {
                    $("#managePermission").html(response);
                }
            }).done(function(response) {
                unblockPage()
                if (response.status == undefined) {
                    $("#PermissionsRol").modal('show')

                    $('#FormPermissionsRol').submit(function(e) {
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
                                $("#PermissionsRol").modal('hide');
                                unblockPage()
                                addToastr(response.type, response.title, response.message)
                            },
                            error: function(response) {
                                unblockPage()
                            }
                        });
                    });
                }
            }).fail(function() {
                unblockPage()
            });
        }

        function archiveRol(rol_id, state) {
            let msj = state == 1 ? "Desea archivar el rol" : "Desea activar el rol"
            let sw = SweetConfirmation(msj, "Si", "No")
            sw.then(response => {
                if (response == true) {
                    blockPage();
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('rol.archive') }}",
                        data: {
                            "_token": "{{ csrf_token() }}",
                            'rol_id': rol_id
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
    </script>
@endpush
