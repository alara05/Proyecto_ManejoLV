@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-sm font-medium text-slate-500">Detalle de ruta</p>
            <h1 class="text-2xl font-semibold">{{ $ruta->nombre }}</h1>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('rutas.edit', $ruta) }}" class="rounded bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">
                Editar
            </a>
            <a href="{{ route('rutas.index') }}" class="rounded border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                Volver
            </a>
        </div>
    </div>

    <section class="grid gap-4 rounded bg-white p-6 text-sm shadow-sm md:grid-cols-2">
        <div>
            <p class="font-medium text-slate-500">Cooperativa</p>
            <p class="mt-1 text-slate-900">{{ $ruta->cooperativa->nombre }}</p>
        </div>
        <div>
            <p class="font-medium text-slate-500">Bus asignado</p>
            <p class="mt-1 text-slate-900">
                {{ $ruta->bus ? 'Bus ' . $ruta->bus->numero . ' - ' . $ruta->bus->placa : 'Sin asignar' }}
            </p>
        </div>
        <div>
            <p class="font-medium text-slate-500">Ciudad origen</p>
            <p class="mt-1 text-slate-900">{{ $ruta->origen->nombre }} - {{ $ruta->origen->provincia }}</p>
        </div>
        <div>
            <p class="font-medium text-slate-500">Ciudad destino</p>
            <p class="mt-1 text-slate-900">{{ $ruta->destino->nombre }} - {{ $ruta->destino->provincia }}</p>
        </div>
        <div>
            <p class="font-medium text-slate-500">Tipo de viaje</p>
            <p class="mt-1 text-slate-900">{{ $ruta->tipo_viaje === 'directo' ? 'Directo' : 'Con paradas' }}</p>
        </div>
        <div>
            <p class="font-medium text-slate-500">Distancia</p>
            <p class="mt-1 text-slate-900">{{ $ruta->distancia_km ? $ruta->distancia_km . ' km' : 'Sin registrar' }}</p>
        </div>
        <div>
            <p class="font-medium text-slate-500">Duracion estimada</p>
            <p class="mt-1 text-slate-900">{{ $ruta->duracion_minutos ? $ruta->duracion_minutos . ' minutos' : 'Sin registrar' }}</p>
        </div>
        <div>
            <p class="font-medium text-slate-500">Estado</p>
            <p class="mt-1 text-slate-900">{{ $ruta->activa ? 'Activa' : 'Inactiva' }}</p>
        </div>
    </section>
@endsection
