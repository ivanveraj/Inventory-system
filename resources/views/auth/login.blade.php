<x-guest-layout>
<<<<<<< HEAD
    <x-jet-authentication-card>
        <x-slot name="logo">
            <x-jet-authentication-card-logo />
        </x-slot>

        <x-jet-validation-errors class="mb-4" />

        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <x-jet-label for="email" value="Email" />
                <x-jet-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                    required autofocus />
            </div>

            <div class="mt-4">
                <x-jet-label for="password" value="Contraseña" />
                <x-jet-input id="password" class="block mt-1 w-full" type="password" name="password" required
                    autocomplete="current-password" />
            </div>

            <div class="block mt-4">
                <label for="remember_me" class="flex items-center">
                    <x-jet-checkbox id="remember_me" name="remember" />
                    <span class="ml-2 text-sm text-gray-600">Recuerdame</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-jet-button class="ml-4">
                    Ingresar
                </x-jet-button>
            </div>
        </form>
    </x-jet-authentication-card>
=======
    <div class="account-pages my-5 pt-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-4 col-lg-6 col-md-8">
                    <div class="card">
                        <div class="card-body p-4">

                            <div class="text-center">
                                <a href="{{ route('dashboard') }}">
                                    <img src="{{ asset('img/sgi.png') }}" alt="Logo SGI"
                                        class="auth-logo logo-dark mx-auto w-20">
                                </a>
                            </div>

                            <h4 class="font-size-18 text-muted mt-2 text-center">¡Bienvenido de nuevo!</h4>
                            <p class="mb-3 text-center">Logueate para continuar con SGI</p>
                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                <div class="grid grid-cols-1 gap-4">
                                    <div class="w-full">
                                        <x-jet-input id="user" class="w-full" type="text" name="user"
                                            placeholder="Usuario" :value="old('user')" required autofocus />
                                    </div>
                                    <div class="w-full">
                                        <x-jet-input id="password" class="w-full" type="password" name="password"
                                            placeholder="Contraseña" required autocomplete="current-password" />
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <label for="remember_me" class="flex items-center">
                                        <x-jet-checkbox id="remember_me" name="remember" />
                                        <span class="ml-2 text-sm text-gray-600">Recuerdame</span>
                                    </label>
                                </div>

                                <div class="d-grid mt-4">
                                    <button class="btn btn-primary waves-effect waves-light"
                                        type="submit">Ingresar</button>
                                </div>
                            </form>
                        </div>

                    </div>
                    <div class="mt-4 text-center">
                        <span>© {{ now()->year }} made by Ivan Vera</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
>>>>>>> ivan
</x-guest-layout>
