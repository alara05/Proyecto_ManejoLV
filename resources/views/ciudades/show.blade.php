@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-sm font-medium text-slate-500">Detalle de ciudad</p>
            <h1 class="text-2xl font-semibold">{{ $ciudad->nombre }}</h1>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('ciudades.edit', $ciudad) }}" class="rounded bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
                Editar
            </a>
            <a href="{{ route('ciudades.index') }}" class="rounded border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Volver
            </a>
        </div>
    </div>

    <section class="rounded bg-white p-6 shadow-sm">
        <div class="mb-6 flex flex-wrap gap-3">
            @include('partials.status-badge', ['active' => $ciudad->activa, 'trueLabel' => 'Activa', 'falseLabel' => 'Inactiva'])
            <span class="rounded bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">{{ $ciudad->provincia->nombre }}</span>
        </div>

        <div class="grid gap-4 text-sm md:grid-cols-2">
            <div>
                <p class="font-semibold text-slate-500">Provincia</p>
                <p class="mt-1 text-slate-900">{{ $ciudad->provincia->nombre }}</p>
            </div>
            <div>
                <p class="font-semibold text-slate-500">Estado</p>
                <p class="mt-1 text-slate-900">{{ $ciudad->activa ? 'Activa' : 'Inactiva' }}</p>
            </div>
            <div>
                <p class="font-semibold text-slate-500">Origenes</p>
                <p class="mt-1 text-slate-900">{{ $ciudad->rutas_origen_count + $ciudad->frecuencias_origen_count }}</p>
            </div>
            <div>
                <p class="font-semibold text-slate-500">Destinos</p>
                <p class="mt-1 text-slate-900">{{ $ciudad->rutas_destino_count + $ciudad->frecuencias_destino_count }}</p>
            </div>
            <div>
                <p class="font-semibold text-slate-500">Paradas</p>
                <p class="mt-1 text-slate-900">{{ $ciudad->paradas_count }}</p>
            </div>
            <div>
                <p class="font-semibold text-slate-500">Boletos asociados</p>
                <p class="mt-1 text-slate-900">{{ $ciudad->boletos_origen_count + $ciudad->boletos_destino_count }}</p>
            </div>
        </div>
    </section>
@endsection
