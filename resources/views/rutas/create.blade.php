@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <p class="text-sm font-medium text-slate-500">Gestion de rutas</p>
        <h1 class="text-2xl font-semibold">Registrar ruta</h1>
    </div>

    <form method="POST" action="{{ route('rutas.store') }}" class="rounded bg-white p-6 shadow-sm">
        @include('rutas._form')
    </form>
@endsection
