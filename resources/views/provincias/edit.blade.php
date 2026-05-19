@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <p class="text-sm font-medium text-slate-500">Editar provincia</p>
        <h1 class="text-2xl font-semibold">{{ $provincia->nombre }}</h1>
    </div>

    <section class="rounded bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('provincias.update', $provincia) }}">
            @method('PUT')
            @include('provincias._form')
        </form>
    </section>
@endsection
