@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-sm font-medium text-slate-500">Detalle del tipo de asiento</p>
            <h1 class="text-2xl font-semibold">{{ $tipoAsiento->nombre }}</h1>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('tipo-asientos.edit', $tipoAsiento) }}" class="rounded bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
                Editar
            </a>
            <a href="{{ route('tipo-asientos.index') }}" class="rounded border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Volver
            </a>
        </div>
    </div>

    <section class="rounded bg-white p-6 shadow-sm">
        <div class="mb-6 flex flex-wrap gap-3">
            @include('partials.status-badge', ['active' => $tipoAsiento->activo])
            <span class="rounded bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">$ {{ number_format((float) $tipoAsiento->recargo, 2) }} recargo</span>
            <span class="rounded bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">{{ $tipoAsiento->asientos_count }} asientos</span>
        </div>

        <div class="grid gap-4 text-sm md:grid-cols-2">
            <div>
                <p class="font-semibold text-slate-500">Cooperativa</p>
                <p class="mt-1 text-slate-900">{{ $tipoAsiento->cooperativa->nombre ?? 'Todas las cooperativas' }}</p>
            </div>
            <div>
                <p class="font-semibold text-slate-500">Recargo</p>
                <p class="mt-1 text-slate-900">$ {{ number_format((float) $tipoAsiento->recargo, 2) }}</p>
            </div>
            <div>
                <p class="font-semibold text-slate-500">Estado</p>
                <p class="mt-1 text-slate-900">{{ $tipoAsiento->activo ? 'Activo' : 'Inactivo' }}</p>
            </div>
            <div>
                <p class="font-semibold text-slate-500">Asientos asociados</p>
                <p class="mt-1 text-slate-900">{{ $tipoAsiento->asientos_count }}</p>
            </div>
            <div class="md:col-span-2">
                <p class="font-semibold text-slate-500">Descripcion</p>
                <p class="mt-1 text-slate-900">{{ $tipoAsiento->descripcion ?? 'Sin registrar' }}</p>
            </div>
        </div>
    </section>
@endsection
