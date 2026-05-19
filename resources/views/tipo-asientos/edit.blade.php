@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <p class="text-sm font-medium text-slate-500">Catalogo de asientos</p>
        <h1 class="text-2xl font-semibold">Editar {{ $tipoAsiento->nombre }}</h1>
    </div>

    <form method="POST" action="{{ route('tipo-asientos.update', $tipoAsiento) }}" class="rounded bg-white p-6 shadow-sm">
        @method('PUT')
        @include('tipo-asientos._form')
    </form>
@endsection
