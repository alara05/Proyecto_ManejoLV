@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-sm font-medium text-slate-500">Hoja de ruta</p>
            <h1 class="text-2xl font-semibold">Salidas</h1>
        </div>
        <a href="{{ route('salidas.create') }}" class="rounded bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
            Nueva salida
        </a>
    </div>

    @include('partials.flash')

    <section class="overflow-hidden rounded bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] border-collapse text-left text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Fecha</th>
                        <th class="px-4 py-3 font-semibold">Frecuencia</th>
                        <th class="px-4 py-3 font-semibold">Cooperativa</th>
                        <th class="px-4 py-3 font-semibold">Bus</th>
                        <th class="px-4 py-3 font-semibold">Precio</th>
                        <th class="px-4 py-3 font-semibold">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($salidas as $salida)
                        <tr class="align-top hover:bg-slate-50/70">
                            <td class="px-4 py-3">
                                <p class="font-semibold text-slate-900">{{ $salida->fecha->format('d/m/Y') }}</p>
                                <p class="text-xs text-slate-500">{{ \Illuminate\Support\Str::of($salida->hora_salida)->substr(0, 5) }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <p>{{ $salida->frecuencia->origen->nombre }} a {{ $salida->frecuencia->destino->nombre }}</p>
                                <p class="text-xs text-slate-500">Frecuencia #{{ $salida->frecuencia->id }}</p>
                            </td>
                            <td class="px-4 py-3">{{ $salida->frecuencia->cooperativa->nombre }}</td>
                            <td class="px-4 py-3">
                                Bus {{ $salida->bus->numero }} - {{ $salida->bus->placa }}
                                <p class="text-xs text-slate-500">{{ $salida->bus->cooperativa->nombre }}</p>
                            </td>
                            <td class="px-4 py-3">${{ number_format($salida->precio_base, 2) }}</td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('salidas.show', $salida) }}" class="rounded border border-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-white">Hoja</a>
                                    <form method="POST" action="{{ route('salidas.destroy', $salida) }}" onsubmit="return confirm('Desea eliminar esta salida?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded border border-red-200 px-2.5 py-1 text-xs font-semibold text-red-700 hover:bg-red-50">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-slate-500">
                                Todavia no hay salidas generadas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="mt-5">
        {{ $salidas->links() }}
    </div>
@endsection
