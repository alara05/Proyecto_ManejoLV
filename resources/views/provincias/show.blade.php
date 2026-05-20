@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-sm font-medium text-slate-500">Detalle de provincia</p>
            <h1 class="text-2xl font-semibold">{{ $provincia->nombre }}</h1>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('provincias.edit', $provincia) }}" class="rounded bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
                Editar
            </a>
            <a href="{{ route('provincias.index') }}" class="rounded border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Volver
            </a>
        </div>
    </div>

    <section class="rounded bg-white p-6 shadow-sm">
        <div class="mb-6 flex flex-wrap gap-3">
            @include('partials.status-badge', ['active' => $provincia->activa, 'trueLabel' => 'Activa', 'falseLabel' => 'Inactiva'])
            <span class="rounded bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">{{ $provincia->ciudades_count }} ciudades</span>
        </div>

        <h2 class="text-sm font-semibold text-slate-500">Ciudades asociadas</h2>
        <div class="mt-3 flex flex-wrap gap-2">
            @forelse ($ciudades as $ciudad)
                <a href="{{ route('ciudades.show', $ciudad) }}" class="rounded border border-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                    {{ $ciudad->nombre }}
                </a>
            @empty
                <p class="text-sm text-slate-500">No hay ciudades asociadas a esta provincia.</p>
            @endforelse
        </div>
    </section>
@endsection
