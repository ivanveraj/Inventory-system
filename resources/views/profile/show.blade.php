@extends('layouts.admin.base')

@section('title', 'Gestion de perfil de usuario')
@section('title_page', 'Gestion de perfil de usuario')

@section('breadcrumb')
    <li class="brea">Gestion de perfil de usuario</li>
@endsection

@push('css')
    <link href="{{ asset('css/admin/select2.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@php
    $LogUser = Auth::user();
@endphp
@section('content')
    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
        @if (Laravel\Fortify\Features::canUpdateProfileInformation())
            @livewire('profile.update-profile-information-form')
            <x-jet-section-border />
        @endif

        @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
            <div class="mt-10 sm:mt-0">
                @livewire('profile.update-password-form')
            </div>
            <x-jet-section-border />
        @endif
    </div>
@endsection
