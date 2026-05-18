@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-sm font-medium text-slate-500">Detalle del asiento</p>
            <h1 class="text-2xl font-semibold">Asiento {{ $asiento->numero }}</h1>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('asientos.edit', $asiento) }}" class="rounded bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
                Editar
            </a>
            <a href="{{ route('asientos.index') }}" class="rounded border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Volver
            </a>
        </div>
    </div>

    <section class="rounded bg-white p-6 shadow-sm">
        <div class="mb-6 flex flex-wrap gap-3">
            @include('partials.status-badge', ['active' => $asiento->activo])
            <span class="rounded bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">Bus {{ $asiento->bus->numero }}</span>
            <span class="rounded bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">{{ $asiento->tipoAsiento->nombre }}</span>
        </div>

        <div class="grid gap-4 text-sm md:grid-cols-2">
            <div>
                <p class="font-semibold text-slate-500">Numero</p>
                <p class="mt-1 text-slate-900">{{ $asiento->numero }}</p>
            </div>
            <div>
                <p class="font-semibold text-slate-500">Bus</p>
                <p class="mt-1 text-slate-900">Bus {{ $asiento->bus->numero }} - {{ $asiento->bus->placa }}</p>
            </div>
            <div>
                <p class="font-semibold text-slate-500">Cooperativa del bus</p>
                <p class="mt-1 text-slate-900">{{ $asiento->bus->cooperativa->nombre }}</p>
            </div>
            <div>
                <p class="font-semibold text-slate-500">Tipo de asiento</p>
                <p class="mt-1 text-slate-900">{{ $asiento->tipoAsiento->nombre }}</p>
            </div>
            <div>
                <p class="font-semibold text-slate-500">Cooperativa del tipo</p>
                <p class="mt-1 text-slate-900">{{ $asiento->tipoAsiento->cooperativa->nombre ?? 'Todas las cooperativas' }}</p>
            </div>
            <div>
                <p class="font-semibold text-slate-500">Estado</p>
                <p class="mt-1 text-slate-900">{{ $asiento->activo ? 'Activo' : 'Inactivo' }}</p>
            </div>
        </div>
    </section>
@endsection
