@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <p class="text-sm font-medium text-slate-500">Mapa de asientos</p>
        <h1 class="text-2xl font-semibold">Editar asiento {{ $asiento->numero }}</h1>
    </div>

    <form method="POST" action="{{ route('asientos.update', $asiento) }}" class="rounded bg-white p-6 shadow-sm">
        @method('PUT')
        @include('asientos._form')
    </form>
@endsection
