<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>MP | @yield('title')</title>

    <link rel="icon" type="image/png" href="{{ asset('img/pool.png') }}" />

    {{--  Font Awesome Icons --}}
    <link href="{{ asset('css/fontawesome/all.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/fontawesome/fontawesome.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/fontawesome/brands.css') }}" rel="stylesheet">
    <link href="{{ asset('css/fontawesome/solid.css') }}" rel="stylesheet">

    <link href="{{ mix('css/app.css') }}" rel="stylesheet" type="text/css">

    <link href="{{ asset('css/bootstrap/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />

    <link href="{{ asset('css/admin/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/styles/style.css') }}" id="app-style" rel="stylesheet" type="text/css" />

    @livewireStyles
    @stack('css')
</head>

<body class="login-page">
    <div class="container">
        <div class="row justify-content-md-center">
            {{ $slot }}
        </div>
    </div>

    @livewireScripts

    <!-- JAVASCRIPT -->
    <script src="{{ mix('js/app.js') }}"></script>

    <script src="{{ asset('js/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/admin/metisMenu.min.js') }}"></script>
    <script src="{{ asset('js/admin/simplebar.min.js') }}"></script>
    <script src="{{ asset('js/admin/waves.min.js') }}"></script>

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
