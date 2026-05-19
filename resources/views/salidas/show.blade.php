@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-sm font-medium text-slate-500">Hoja de ruta</p>
            <h1 class="text-2xl font-semibold">
                {{ $salida->frecuencia->origen->nombre }} a {{ $salida->frecuencia->destino->nombre }}
            </h1>
        </div>
        <div class="flex gap-2">
            <button type="button" onclick="window.print()" class="rounded bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
                Imprimir
            </button>
            <a href="{{ route('salidas.index') }}" class="rounded border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Volver
            </a>
        </div>
    </div>

    @include('partials.flash')

    <section class="rounded bg-white p-6 shadow-sm">
        <div class="mb-6 flex flex-wrap gap-3">
            <span class="rounded bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">
                {{ $salida->fecha->format('d/m/Y') }}
            </span>
            <span class="rounded bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">
                {{ \Illuminate\Support\Str::of($salida->hora_salida)->substr(0, 5) }}
            </span>
            <span class="rounded bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">
                {{ ucfirst(str_replace('_', ' ', $salida->estado)) }}
            </span>
        </div>

        <div class="grid gap-4 text-sm md:grid-cols-2">
            <div>
                <p class="font-semibold text-slate-500">Cooperativa</p>
                <p class="mt-1 text-slate-900">{{ $salida->frecuencia->cooperativa->nombre }}</p>
            </div>
            <div>
                <p class="font-semibold text-slate-500">Bus asignado</p>
                <p class="mt-1 text-slate-900">Bus {{ $salida->bus->numero }} - {{ $salida->bus->placa }}</p>
                <p class="text-xs text-slate-500">{{ $salida->bus->cooperativa->nombre }}</p>
            </div>
            <div>
                <p class="font-semibold text-slate-500">Origen</p>
                <p class="mt-1 text-slate-900">{{ $salida->frecuencia->origen->nombre }} - {{ $salida->frecuencia->origen->provincia->nombre }}</p>
            </div>
            <div>
                <p class="font-semibold text-slate-500">Destino</p>
                <p class="mt-1 text-slate-900">{{ $salida->frecuencia->destino->nombre }} - {{ $salida->frecuencia->destino->provincia->nombre }}</p>
            </div>
            <div>
                <p class="font-semibold text-slate-500">Precio base</p>
                <p class="mt-1 text-slate-900">${{ number_format($salida->precio_base, 2) }}</p>
            </div>
            <div>
                <p class="font-semibold text-slate-500">Generacion</p>
                <p class="mt-1 text-slate-900">{{ $salida->generada_automaticamente ? 'Automatica' : 'Manual' }}</p>
            </div>
        </div>

        <div class="mt-8">
            <h2 class="text-lg font-semibold">Recorrido</h2>
            <div class="mt-3 overflow-hidden rounded border border-slate-200">
                <table class="w-full min-w-[520px] border-collapse text-left text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Orden</th>
                            <th class="px-4 py-3 font-semibold">Ciudad</th>
                            <th class="px-4 py-3 font-semibold">Minutos desde origen</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr>
                            <td class="px-4 py-3">0</td>
                            <td class="px-4 py-3">{{ $salida->frecuencia->origen->nombre }} - {{ $salida->frecuencia->origen->provincia->nombre }}</td>
                            <td class="px-4 py-3">0</td>
                        </tr>
                        @foreach ($salida->frecuencia->paradas as $parada)
                            <tr>
                                <td class="px-4 py-3">{{ $parada->orden }}</td>
                                <td class="px-4 py-3">{{ $parada->ciudad->nombre }} - {{ $parada->ciudad->provincia->nombre }}</td>
                                <td class="px-4 py-3">{{ $parada->minutos_desde_origen }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td class="px-4 py-3">Final</td>
                            <td class="px-4 py-3">{{ $salida->frecuencia->destino->nombre }} - {{ $salida->frecuencia->destino->provincia->nombre }}</td>
                            <td class="px-4 py-3">-</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
