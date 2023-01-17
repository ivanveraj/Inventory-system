@extends('layouts.admin.base')

@section('title', 'Historial de ventas')
@section('title_page', 'Historial de ventas')

@section('breadcrumb')
    <li class="text-size-sm pl-2 capitalize leading-normal text-slate-700 before:float-left before:pr-2 before:text-gray-600 before:content-['/']"
        aria-current="page">Historial de ventas</li>
@endsection

@push('css')
    <link href="{{ asset('css/admin/select2.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('content')
    <x-card>
        dsadasdas
    </x-card>
@endsection
@push('js')
    <script src="{{ asset('js/admin/sweetalert2.js') }}"></script>
    <script src="{{ asset('js/admin/select2.min.js') }}"></script>
    <script></script>
@endpush
