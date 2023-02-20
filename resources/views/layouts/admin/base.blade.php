<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>MP | @yield('title')</title>

    <link rel="icon" type="image/png" href="{{ asset('img/pool.png') }}" />

    {{--  Font Awesome Icons --}}
    <link href="{{ asset('css/fontawesome/all.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/fontawesome/fontawesome.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/fontawesome/brands.css') }}" rel="stylesheet">
    <link href="{{ asset('css/fontawesome/solid.css') }}" rel="stylesheet">

    <link href="{{ asset('libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />

    <link href="{{ asset('libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />

    <link href="{{ mix('css/app.css') }}" rel="stylesheet" type="text/css">

    <link href="{{ asset('css/bootstrap/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet"
        type="text/css" />

    <link href="{{ asset('css/admin/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
    {{--  <link href="{{ asset('css/icons.css') }}?version=1.5" rel="stylesheet" type="text/css" /> --}}
    <link href="{{ asset('css/styles/style.css') }}" id="app-style" rel="stylesheet" type="text/css" />

    @livewireStyles
    @stack('css')
</head>

<body data-sidebar="dark" data-topbar="dark">

    <div id="layout-wrapper">

        @include('layouts.admin.navbar')

        @include('layouts.admin.sidebar')


        <div class="main-content">
            <div class="page-content">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                <h5 class="mb-0">@yield('title_page')</h5>
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item">
                                            <a href="{{ route('dashboard') }}" class="breadcrumb-item">Dashboard</a>
                                        </li>
                                        @yield('breadcrumb')
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="w-full pt-4 mx-auto">
                        @yield('content')
                    </div>

                </div>
            </div>

            @include('layouts.admin.footer')

        </div>
    </div>

    <div class="loader_wrapper">
        <span class="loader">
            <span class="loader-inner"></span>
        </span>
    </div>

    @livewireScripts

    <!-- JAVASCRIPT -->
    <script src="{{ mix('js/app.js') }}"></script>

    <script src="{{ asset('js/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/admin/metisMenu.min.js') }}"></script>
    <script src="{{ asset('js/admin/simplebar.min.js') }}"></script>
    <script src="{{ asset('js/admin/waves.min.js') }}"></script>


    <script src="{{ asset('libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <!-- Buttons examples -->
    <script src="{{ asset('libs/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('libs/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('libs/pdfmake/build/pdfmake.min.js') }}"></script>
    <script src="{{ asset('libs/pdfmake/build/vfs_fonts.js') }}"></script>
    <script src="{{ asset('libs/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('libs/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('libs/datatables.net-buttons/js/buttons.colVis.min.js') }}"></script>

    <!-- Responsive examples -->
    <script src="{{ asset('libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>

    <script src="{{ asset('js/datatable/dataTables.responsive.min.js') }}"></script>
    {{--     <script src="{{ asset('js/datatable/jszip.min.js') }}"></script>
    <script src="{{ asset('js/datatable/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('js/datatable/buttons.print.min.js') }}"></script> --}}
    <script src="{{ asset('js/datatable/langs.js') }}"></script>

    <script src="{{ asset('js/admin/appTemplate.js') }}"></script>

    <script src="{{ asset('js/scripts/general.js') }}"></script>

    @stack('js2')

    @stack('modals')

    <script>
        $(window).on('load', function() {
            $(".loader_wrapper").fadeOut("slow");
        })
    </script>

    @stack('js')
</body>

</html>
