@extends('layouts.admin.base')

@section('title', 'Configuraciones general')
@section('title_page', 'Configuraciones general')

@section('breadcrumb')
    <li class="text-size-sm pl-2 capitalize leading-normal text-slate-700 before:float-left before:pr-2 before:text-gray-600 before:content-['/']"
        aria-current="page">Configuraciones general</li>
@endsection

@section('content')
    <x-card>
        <form action="{{ route('settings.general') }}" method="POST" id="pool">
            @csrf
            <div class="card card-primary">
                <h5 class="card-header bg-primary"></h5>
                <div class="card-body">
                    <div class="grid md:grid-cols-3 gap-4 justify-center align-middle md:px-36 sm:px-0 sm:grid-cols-1">
                        @foreach ($general as $conf)
                            <div class="w-full">
                                <x-jet-label value="{{ $conf->key }}"></x-jet-label>
                                <x-jet-input value="{{ $conf->value }}" type="number" name="{{ $conf->key }}"
                                    class="w-full">
                                </x-jet-input>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="card-footer">
                    <x-jet-button>Guardar</x-jet-button>
                </div>
            </div>
        </form>
    </x-card>

@endsection

@push('js')
    <script>
        $('#pool').submit(function(e) {
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
                },
                error: function() {
                    unblockPage();
                },
            });
        });
    </script>
@endpush
