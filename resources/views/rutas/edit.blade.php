@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <p class="text-sm font-medium text-slate-500">Gestion de rutas</p>
        <h1 class="text-2xl font-semibold">Editar ruta {{ $ruta->nombre }}</h1>
    </div>

    <form method="POST" action="{{ route('rutas.update', $ruta) }}" class="rounded bg-white p-6 shadow-sm">
        @method('PUT')
        @include('rutas._form')
    </form>
@endsection
