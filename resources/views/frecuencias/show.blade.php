@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-sm font-medium text-slate-500">Detalle de frecuencia ANT</p>
            <h1 class="text-2xl font-semibold">{{ $frecuencia->origen->nombre }} a {{ $frecuencia->destino->nombre }}</h1>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('frecuencias.edit', $frecuencia) }}" class="rounded bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
                Editar
            </a>
            <a href="{{ route('frecuencias.index') }}" class="rounded border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Volver
            </a>
        </div>
    </div>

    <section class="grid gap-4 rounded bg-white p-6 text-sm shadow-sm md:grid-cols-2">
        <div>
            <p class="font-semibold text-slate-500">Cooperativa</p>
            <p class="mt-1 text-slate-900">{{ $frecuencia->cooperativa->nombre }}</p>
        </div>
        <div>
            <p class="font-semibold text-slate-500">Hora autorizada</p>
            <p class="mt-1 text-slate-900">{{ substr($frecuencia->hora_salida, 0, 5) }}</p>
        </div>
        <div>
            <p class="font-semibold text-slate-500">Origen</p>
            <p class="mt-1 text-slate-900">{{ $frecuencia->origen->nombre }} - {{ $frecuencia->origen->provincia->nombre }}</p>
        </div>
        <div>
            <p class="font-semibold text-slate-500">Destino</p>
            <p class="mt-1 text-slate-900">{{ $frecuencia->destino->nombre }} - {{ $frecuencia->destino->provincia->nombre }}</p>
        </div>
        <div>
            <p class="font-semibold text-slate-500">Resolucion ANT</p>
            <p class="mt-1 text-slate-900">{{ $frecuencia->numero_resolucion_ant ?? 'Sin registrar' }}</p>
        </div>
        <div>
            <p class="font-semibold text-slate-500">Fecha de resolucion</p>
            <p class="mt-1 text-slate-900">{{ $frecuencia->fecha_resolucion_ant?->format('Y-m-d') ?? 'Sin registrar' }}</p>
        </div>
        <div>
            <p class="font-semibold text-slate-500">Estado</p>
            <p class="mt-1 text-slate-900">{{ $frecuencia->activa ? 'Activa' : 'Inactiva' }}</p>
        </div>
        <div>
            <p class="font-semibold text-slate-500">Tipo</p>
            <p class="mt-1 text-slate-900">{{ $frecuencia->tiene_paradas ? 'Con paradas intermedias' : 'Directa' }}</p>
        </div>
    </section>

    <section class="mt-6 rounded bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold">Paradas intermedias</h2>

        @if ($frecuencia->paradas->isNotEmpty())
            <div class="mt-4 overflow-x-auto">
                <table class="w-full min-w-[560px] border-collapse text-left text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Orden</th>
                            <th class="px-4 py-3 font-semibold">Ciudad</th>
                            <th class="px-4 py-3 font-semibold">Tiempo desde origen</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($frecuencia->paradas as $parada)
                            <tr>
                                <td class="px-4 py-3">{{ $parada->orden }}</td>
                                <td class="px-4 py-3">{{ $parada->ciudad->nombre }} - {{ $parada->ciudad->provincia->nombre }}</td>
                                <td class="px-4 py-3">{{ $parada->minutos_desde_origen }} minutos</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="mt-3 text-sm text-slate-500">Esta frecuencia no tiene paradas intermedias registradas.</p>
        @endif
    </section>
@endsection
