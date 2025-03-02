@extends('layouts.admin.base')

@section('title', 'Gestion de ventas')
@section('title_page', 'Gestion de ventas')

@section('breadcrumb')
    <li class="text-size-sm pl-2 capitalize leading-normal text-slate-700 before:float-left before:pr-2 before:text-gray-600 before:content-['/']"
        aria-current="page">Gestion de ventas</li>
@endsection

@push('css')
    <link href="{{ asset('css/admin/select2.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('content')
    <x-card>
        <div class="flex justify-center my-4">
            @if (!$day)
                <x-jet-button type="button" class="finish_day" onclick="initDay()">Iniciar el dia
                </x-jet-button>
            @else
                <x-jet-danger-button type="button" class="finish_day" onclick="finishDay()">Finalizar el dia
                </x-jet-danger-button>
            @endif
        </div>

        @if ($day)
            <div id="table_sale"></div>
            <hr class="h-2 mx-0 my-3 bg-black" />
            <h5 class="text-center">Ventas diarias</h5>
            <div class="flex justify-center my-6">
                <x-jet-button type="button" onclick="newSaleGeneral()">Nueva venta</x-jet-button>
            </div>
            <div id="table_general"></div>
        @endif
    </x-card>
    <div id="contModal"></div>
@endsection
@push('js')
    <script src="{{ asset('js/admin/sweetalert2.js') }}"></script>
    <script src="{{ asset('js/admin/select2.min.js') }}"></script>
    <script>
        $(function() {
            reloadTable()
            reloadGeneral()
        });

        function reloadTable(res = true) {
            $.get(`{{ route('sales.tablesSales') }}`, function(response) {
                $("#table_sale").html(response);
            }).done(function(response) {
                $('#sales').DataTable().destroy()
                $('#sales').DataTable({
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
                    }],
                    "drawCallback": function(settings) {
                        $('[data-toggle="tooltip"]').tooltip();
                    }
                })

                $.get(`{{ route('sale.dataGeneral') }}?type=${1}`, function(response) {
                    let general = response.general
                    $.each(general, function(i, value) {
                        let general_id = value.id
                        initSelectProduct("#selectProduct_" + general_id,
                            "{{ route('sale.products') }}")
                        initFormAddProduct(general_id, 1)

                        $.each(value.extras, function(i, value) {
                            $("#amountExtra_" + value.id).on('input', function() {
                                if ($(this).val() < 0) {
                                    $(this).val(0)
                                }
                                somechange(general_id, value.id, $(this).val())
                            });
                        });
                    });
                })

                if (res) {
                    setTimeout(() => {
                        reloadTable()
                    }, 80000);
                }
            });
        }

        function reloadGeneral() {
            $.get(`{{ route('sales.generalSale') }}`, function(response) {
                $("#table_general").html(response);
            }).done(function(response) {
                unblockPage()

                $('#general').DataTable().destroy()
                $('#general').DataTable({
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
                    }],
                    "drawCallback": function(settings) {
                        $('[data-toggle="tooltip"]').tooltip();
                    }
                })

                $.get(`{{ route('sale.dataGeneral') }}?type=${2}`, function(response) {
                    let general = response.general
                    $.each(general, function(i, value) {
                        let general_id = value.id
                        initSelectProduct("#selectProduct_" + general_id,
                            "{{ route('sale.products') }}")
                        initFormAddProduct(general_id, 2)

                        $.each(value.extras, function(i, value) {
                            $("#amountExtra_" + value.id).on('input', function() {
                                if ($(this).val() < 0) {
                                    $(this).val(0)
                                }
                                somechange(general_id, value.id, $(this).val())
                            });
                        });
                    });
                })
            }).fail(function() {
                unblockPage()
            });
        }

        function somechange(i, extra_id, amount) {
            $.ajax({
                type: "GET",
                dataType: "json",
                url: "{{ route('sale.changeAmountExtra') }}",
                data: {
                    'amount': amount,
                    'extra_id': extra_id
                },
                success: function(r) {
                    if (r.status == 1) {
                        $('#totalExtra_' + i).html(r.data);
                    } else {
                        addToastr(r.type, r.title, r.message)
                    }
                },
                error: function(r) {
                    console.log(r);
                }
            });
        }

        function startTime(sale_id) {
            blockPage();
            $.ajax({
                type: 'POST',
                url: "{{ route('sale.startTime') }}",
                data: {
                    "_token": "{{ csrf_token() }}",
                    'sale_id': sale_id
                },
                dataType: "json",
                success: function(response) {
                    unblockPage()
                    addToastr(response.type, response.title, response.message)
                    if (response.status == 1) {
                        $(".startTime_" + sale_id).hide();
                        reloadTable(false);
                    }
                },
                error: function(response) {
                    unblockPage()
                }
            });
        }

        function newSaleGeneral() {
            blockPage();
            $.ajax({
                type: 'POST',
                url: "{{ route('sale.newSaleGeneral') }}",
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                dataType: "json",
                success: function(response) {
                    unblockPage()
                    addToastr(response.type, response.title, response.message)
                    reloadGeneral();
                },
                error: function(response) {
                    unblockPage()
                }
            });
        }

        function initFormAddProduct(id, type) {
            $('#formAddProduct_' + id).submit(function(e) {
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
                        if (response.status == 1) {
                            if (type == 1) {
                                reloadTable(false)
                            } else {
                                reloadGeneral()
                            }
                        } else {
                            addToastr(response.type, response.title, response.message)
                        }
                        unblockPage()
                    },
                    error: function(response) {
                        unblockPage()
                    }
                });
            });
        }

        function deleteExtra(extra_id, type) {
            $.ajax({
                type: "POST",
                dataType: "json",
                url: "{{ route('sale.deleteExtra') }}",
                data: {
                    "_token": "{{ csrf_token() }}",
                    'extra_id': extra_id,
                },
                success: function(response) {
                    if (response.status == 1) {
                        if (type == 1) {
                            reloadTable(false)
                        } else {
                            reloadGeneral()
                        }
                    } else {
                        addToastr(response.type, response.title, response.message)
                    }
                },
                error: function(response) {
                    console.log(response);
                }
            });
        }

        function viewDetail(sale_id, type) {
            $.get(`{{ route('sale.detail') }}/${sale_id}`, function(response) {
                $("#contModal").html(response);
            }).done(function() {
                unblockPage();

                $("#modalPayment").modal('show')

                $('#detailTable').DataTable().destroy()
                $('#detailTable').DataTable({
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

                $('#FormPayment').submit(function(e) {
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
                            addToastr(response.type, response.title, response.message)
                            if (response.status == 1) {
                                $("#modalPayment").modal('hide');
                                if (type == 1) {
                                    reloadTable(false)
                                } else {
                                    reloadGeneral()
                                }
                            }
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

        function initChangeClient(general_id) {
            $.ajax({
                type: "GET",
                dataType: "json",
                url: "{{ route('sale.changeNameClient') }}",
                data: {
                    'sale_id': general_id,
                    'client': $("#client_" + general_id).val()
                },
                success: function(r) {
                    if (r.status == 0) {
                        addToastr(r.type, r.title, r.message)
                    }
                },
                error: function(r) {
                    console.log(r);
                }
            });
        }

        function finishDay() {
            let sw = SweetConfirmation("Desea finalizar las ventas diarias Nota: NO es reversible",
                "Si, deseo cerrar las ventas", "Cancelar")
            sw.then(response => {
                if (response == true) {
                    $.ajax({
                        type: 'GET',
                        url: "{{ route('sale.finishDay') }}",
                        dataType: "json",
                        success: function(response) {
                            if (response.status == 1) {
                                $('.finish_day').removeClass('hidden')
                                $('.init_day').addClass('hidden')
                                location.href = "{{ route('dashboard') }}";
                            } else {
                                addToastr(response.type, response.title, response.message)
                            }
                        }
                    });
                }
            })
        }

        function initDay() {
            let sw = SweetConfirmation("Desea iniciar las ventas del dia",
                "Si, deseo iniciar las ventas", "Cancelar")
            sw.then(response => {
                if (response == true) {
                    $.ajax({
                        type: 'GET',
                        url: "{{ route('sale.initDay') }}",
                        dataType: "json",
                        success: function(response) {
                            if (response.status == 1) {
                                location.reload();
                            }
                        }
                    });
                }
            })
        }
    </script>
@endpush
