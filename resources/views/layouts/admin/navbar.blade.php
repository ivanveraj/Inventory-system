@php
    $LogUser = Auth::user();
@endphp
<header id="page-topbar">
    <div class="navbar-header">
        <div class="d-flex">
            <div class="navbar-brand-box text-center d-flex justify-center items-center">
                <a href="{{ route('dashboard') }}" class="logo logo-light d-flex">
                    <span class="logo-sm">
                        <img src="{{ asset('img/sgi.png') }}" alt="logo-sm-light" class="w-12">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('img/sgi.png') }}" alt="logo-light" class="w-20">
                    </span>
                </a>
            </div>

            <button type="button" class="btn btn-sm px-3 font-size-24 header-item waves-effect" id="vertical-menu-btn">
                <i class="fas fa-bars align-middle"></i>
            </button>
        </div>

        <div class="d-flex">
            <div class="dropdown d-inline-block user-dropdown">
                <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <div class="flex justify-center align-middle">
                        <img class="rounded-circle w-10 h-10" src="{{ $LogUser->profile_photo_url }}">
                        <div class="flex items-center">
                            <span class="d-none d-xl-inline-block ms-1">{{ $LogUser->name }}</span>
                            <i class="fas fa-caret-down d-none d-xl-inline-block ml-2"></i>
                        </div>
                    </div>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <a class="dropdown-item" href="{{ route('profile.show') }}">
                        <i class="ri-user-line align-middle me-1"></i>
                        Administrar perfil</a>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}" x-data>
                        @csrf
                        <a class="dropdown-item" href="{{ route('logout') }}" @click.prevent="$root.submit();">
                            <i class="ri-shut-down-line align-middle me-1 text-danger"></i>
                            Cerrar sesion</a>
                    </form>
                </div>
            </div>


        </div>
    </div>
</header>
