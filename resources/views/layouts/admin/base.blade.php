<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('img/apple-icon.png') }}" />
    <link rel="icon" type="image/png" href="{{ asset('img/pool.png') }}" />
    <title>MP | @yield('title')</title>


    <!-- Font Awesome Icons -->
    <link href="{{ asset('css/fontawesome/all.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/fontawesome/fontawesome.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/fontawesome/brands.css') }}" rel="stylesheet">
    <link href="{{ asset('css/fontawesome/solid.css') }}" rel="stylesheet">
    <!-- Nucleo Icons -->
    <link href="{{ asset('css/admin/nucleo-icons.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/admin/nucleo-svg.css') }}" rel="stylesheet" />
    <script src="{{ asset('js/admin/plugins/popper.min.js') }}"></script>


    <link href="{{ asset('css/bootstrap/bootstrap.css') }}"rel="stylesheet" type="text/css" />

    <link href="{{ asset('css/datatable/jquery.dataTables.min.css') }}" rel="stylesheet" />

    <link href="{{ mix('css/app.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('css/admin/styles.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/styles/style.css') }}" rel="stylesheet" type="text/css" />
    @livewireStyles

    @stack('css')
</head>

<body class="m-0 font-sans antialiased font-normal text-size-base leading-default bg-gray-200 text-black">
    <!-- sidenav  -->
    @include('layouts.admin.sidebar')
    <!-- end sidenav -->

    <main class="ease-soft-in-out xl:ml-68.5 relative h-full max-h-screen rounded-xl transition-all duration-200">
        <!-- Navbar -->

        @include('layouts.admin.navbar')

        @yield('content')
    </main>

    @include('layouts.admin.configuration')

    <div class="loader_wrapper">
        <span class="loader">
            <span class="loader-inner"></span>
        </span>
    </div>

    @livewireScripts

    <!-- Scripts -->
    <script src="{{ mix('js/app.js') }}"></script>
    <script src="{{ asset('js/bootstrap/bootstrap.js') }}"></script>



    <script src="{{ asset('js/admin/plugins/perfect-scrollbar.min.js') }}"></script>
    {{--  <script async defer src="https://buttons.github.io/buttons.js"></script> --}}
    <script src="{{ asset('js/admin/soft.js') }}"></script>
    <script src="{{ asset('js/scripts/general.js') }}"></script>

    <script src="{{ asset('js/datatable/jquery.dataTables.min.js') }}"></script>

    <script>
        $(window).on('load', function() {
            $(".loader_wrapper").fadeOut("slow");
        })
    </script>
    @stack('js')
</body>


</html>
