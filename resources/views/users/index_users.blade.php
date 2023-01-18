@extends('layouts.admin.base')

@section('title', 'Listado de usuarios')
@section('title_page', 'Listado de usuarios')

@section('breadcrumb')
    <li class="text-size-sm pl-2 capitalize leading-normal text-slate-700 before:float-left before:pr-2 before:text-gray-600 before:content-['/']"
        aria-current="page">Listado de usuarios</li>
@endsection

@push('css')
    <link href="{{ asset('css/admin/select2.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@php
$LogUser = Auth::user();
@endphp
@section('content')
    <x-card>
        <div class="flex justify-between my-3 p-2">
            <div>
                <button type="button" onclick="reloadTable()" data-toggle="tooltip" data-placement="top" title="Recargar">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
            <div>
                <x-jet-button type="button" onclick="create()">Crear un nuevo usuario</x-jet-button>
            </div>
        </div>

        <table id="Table" class="p-4 items-center w-full align-top border-gray-200 text-slate-500 text-center">
            <thead>
                <tr
                    class="px-6 py-3 font-bold uppercase align-middle border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">
                    <th>Nombre</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
               
            </tbody>
        </table>
    </x-card>

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
            $('#Table').DataTable().destroy()
            $('#Table').DataTable({
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
                }]
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
