@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-sm font-medium text-slate-500">Frecuencias autorizadas por la ANT</p>
            <h1 class="text-2xl font-semibold">Frecuencias</h1>
        </div>
        <a href="{{ route('frecuencias.create') }}" class="rounded bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
            Nueva frecuencia
        </a>
    </div>

    @include('partials.flash')

    <section class="overflow-hidden rounded bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[980px] border-collapse text-left text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Cooperativa</th>
                        <th class="px-4 py-3 font-semibold">Trayecto</th>
                        <th class="px-4 py-3 font-semibold">Hora</th>
                        <th class="px-4 py-3 font-semibold">Resolucion ANT</th>
                        <th class="px-4 py-3 font-semibold">Paradas</th>
                        <th class="px-4 py-3 font-semibold">Estado</th>
                        <th class="px-4 py-3 font-semibold">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($frecuencias as $frecuencia)
                        <tr class="align-top hover:bg-slate-50/70">
                            <td class="px-4 py-3 font-semibold text-slate-900">{{ $frecuencia->cooperativa->nombre }}</td>
                            <td class="px-4 py-3">
                                <p>{{ $frecuencia->origen->nombre }} a {{ $frecuencia->destino->nombre }}</p>
                                <p class="text-xs text-slate-500">{{ $frecuencia->origen->provincia->nombre }} - {{ $frecuencia->destino->provincia->nombre }}</p>
                            </td>
                            <td class="px-4 py-3">{{ substr($frecuencia->hora_salida, 0, 5) }}</td>
                            <td class="px-4 py-3">
                                <p>{{ $frecuencia->numero_resolucion_ant ?? 'Sin registrar' }}</p>
                                <p class="text-xs text-slate-500">{{ $frecuencia->fecha_resolucion_ant?->format('Y-m-d') ?? 'Sin fecha' }}</p>
                            </td>
                            <td class="px-4 py-3">
                                {{ $frecuencia->paradas->count() }}
                            </td>
                            <td class="px-4 py-3">
                                @include('partials.status-badge', ['active' => $frecuencia->activa, 'trueLabel' => 'Activa', 'falseLabel' => 'Inactiva'])
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('frecuencias.show', $frecuencia) }}" class="rounded border border-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-white">Ver</a>
                                    <a href="{{ route('frecuencias.edit', $frecuencia) }}" class="rounded border border-blue-200 px-2.5 py-1 text-xs font-semibold text-blue-700 hover:bg-blue-50">Editar</a>
                                    <form method="POST" action="{{ route('frecuencias.destroy', $frecuencia) }}" onsubmit="return confirm('Desea eliminar esta frecuencia?')">
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
                                Todavia no hay frecuencias registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="mt-5">
        {{ $frecuencias->links() }}
    </div>
@endsection
