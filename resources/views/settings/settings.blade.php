@extends('layouts.admin.base')

@section('title', 'Configuraciones general')
@section('title_page', 'Configuraciones general')

@section('breadcrumb')
    {{--  <li class="breadcrumb-item">
        <a href="{{ route('ticket.detail', $ticket->id) }}" class="breadcrumb-item">{{ __('tickets.detalle_ticket') }}</a>
    </li> --}}
    <li class="breadcrumb-item active">Configuraciones general</li>
@endsection

@section('content')
        <form action="{{ route('settings.general') }}" method="POST" id="pool">
            @csrf
            <div class="card mb-3">
                <div class="card-body">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 justify-center align-middle ">
                        @foreach ($general as $conf)
                            <div>
                                <x-jet-label value="{{ $conf->key }}"></x-jet-label>
                                <x-jet-input placeholder="{{ $conf->value }}" type="number" name="{{ $conf->key }}" class="w-full">
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
