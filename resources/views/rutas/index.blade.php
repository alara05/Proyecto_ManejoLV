@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-sm font-medium text-slate-500">Gestion de rutas</p>
            <h1 class="text-2xl font-semibold">Rutas</h1>
        </div>
        <a href="{{ route('rutas.create') }}" class="rounded bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
            Nueva ruta
        </a>
    </div>

    @include('partials.flash')

    <section class="overflow-hidden rounded bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[860px] border-collapse text-left text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Ruta</th>
                        <th class="px-4 py-3 font-semibold">Cooperativa</th>
                        <th class="px-4 py-3 font-semibold">Trayecto</th>
                        <th class="px-4 py-3 font-semibold">Bus</th>
                        <th class="px-4 py-3 font-semibold">Tipo</th>
                        <th class="px-4 py-3 font-semibold">Estado</th>
                        <th class="px-4 py-3 font-semibold">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($rutas as $ruta)
                        <tr class="align-top hover:bg-slate-50/70">
                            <td class="px-4 py-3 font-semibold text-slate-900">{{ $ruta->nombre }}</td>
                            <td class="px-4 py-3">{{ $ruta->cooperativa->nombre }}</td>
                            <td class="px-4 py-3">
                                <p>{{ $ruta->origen->nombre }}</p>
                                <p class="text-xs text-slate-500">{{ $ruta->origen->provincia->nombre }} a {{ $ruta->destino->nombre }}, {{ $ruta->destino->provincia->nombre }}</p>
                            </td>
                            <td class="px-4 py-3">
                                {{ $ruta->bus ? 'Bus ' . $ruta->bus->numero . ' - ' . $ruta->bus->placa : 'Sin asignar' }}
                            </td>
                            <td class="px-4 py-3">{{ $ruta->tipo_viaje === 'directo' ? 'Directo' : 'Con paradas' }}</td>
                            <td class="px-4 py-3">
                                @include('partials.status-badge', ['active' => $ruta->activa, 'trueLabel' => 'Activa', 'falseLabel' => 'Inactiva'])
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('rutas.show', $ruta) }}" class="rounded border border-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-white">Ver</a>
                                    <a href="{{ route('rutas.edit', $ruta) }}" class="rounded border border-blue-200 px-2.5 py-1 text-xs font-semibold text-blue-700 hover:bg-blue-50">Editar</a>
                                    <form method="POST" action="{{ route('rutas.destroy', $ruta) }}" onsubmit="return confirm('Desea eliminar esta ruta?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded border border-red-200 px-2.5 py-1 text-xs font-semibold text-red-700 hover:bg-red-50">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-slate-500">
                                Todavia no hay rutas registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="mt-5">
        {{ $rutas->links() }}
    </div>
@endsection
