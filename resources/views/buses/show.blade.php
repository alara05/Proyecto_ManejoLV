@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-sm font-medium text-slate-500">Detalle del bus</p>
            <h1 class="text-2xl font-semibold">Bus {{ $bus->numero }}</h1>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('buses.edit', $bus) }}" class="rounded bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
                Editar
            </a>
            <a href="{{ route('buses.index') }}" class="rounded border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Volver
            </a>
        </div>
    </div>

    <section class="rounded bg-white p-6 shadow-sm">
        <div class="mb-6 flex flex-wrap gap-3">
            @include('partials.status-badge', ['active' => $bus->activo])
            <span class="rounded bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">{{ $bus->capacidad_total }} asientos</span>
            <span class="rounded bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">{{ $bus->placa }}</span>
        </div>

        <div class="grid gap-4 text-sm md:grid-cols-2">
        <div>
            <p class="font-semibold text-slate-500">Cooperativa</p>
            <p class="mt-1 text-slate-900">{{ $bus->cooperativa->nombre }}</p>
        </div>
        <div>
            <p class="font-semibold text-slate-500">Placa</p>
            <p class="mt-1 text-slate-900">{{ $bus->placa }}</p>
        </div>
        <div>
            <p class="font-semibold text-slate-500">Marca del chasis</p>
            <p class="mt-1 text-slate-900">{{ $bus->marca_chasis ?? 'Sin registrar' }}</p>
        </div>
        <div>
            <p class="font-semibold text-slate-500">Marca de carroceria</p>
            <p class="mt-1 text-slate-900">{{ $bus->marca_carroceria ?? 'Sin registrar' }}</p>
        </div>
        <div>
            <p class="font-semibold text-slate-500">Anio</p>
            <p class="mt-1 text-slate-900">{{ $bus->anio ?? 'Sin registrar' }}</p>
        </div>
        <div>
            <p class="font-semibold text-slate-500">Capacidad total</p>
            <p class="mt-1 text-slate-900">{{ $bus->capacidad_total }}</p>
        </div>
        <div>
            <p class="font-semibold text-slate-500">Fotografia</p>
            <p class="mt-1 text-slate-900">{{ $bus->foto_path ?? 'Sin registrar' }}</p>
        </div>
        <div>
            <p class="font-semibold text-slate-500">Estado</p>
            <p class="mt-1 text-slate-900">{{ $bus->activo ? 'Activo' : 'Inactivo' }}</p>
        </div>
        </div>
    </section>
@endsection
