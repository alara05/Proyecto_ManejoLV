@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <p class="text-sm font-medium text-slate-500">Editar ciudad</p>
        <h1 class="text-2xl font-semibold">{{ $ciudad->nombre }}</h1>
    </div>

    <section class="rounded bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('ciudades.update', $ciudad) }}">
            @method('PUT')
            @include('ciudades._form')
        </form>
    </section>
@endsection
