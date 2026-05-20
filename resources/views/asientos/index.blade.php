@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-sm font-medium text-slate-500">Mapa de asientos</p>
            <h1 class="text-2xl font-semibold">Asientos</h1>
        </div>
        <a href="{{ route('asientos.create') }}" class="rounded bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
            Nuevo asiento
        </a>
    </div>

    @include('partials.flash')

    <section class="overflow-hidden rounded bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[820px] border-collapse text-left text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Asiento</th>
                        <th class="px-4 py-3 font-semibold">Bus</th>
                        <th class="px-4 py-3 font-semibold">Cooperativa</th>
                        <th class="px-4 py-3 font-semibold">Tipo</th>
                        <th class="px-4 py-3 font-semibold">Estado</th>
                        <th class="px-4 py-3 font-semibold">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($asientos as $asiento)
                        <tr class="align-top hover:bg-slate-50/70">
                            <td class="px-4 py-3 font-semibold text-slate-900">{{ $asiento->numero }}</td>
                            <td class="px-4 py-3">Bus {{ $asiento->bus->numero }} - {{ $asiento->bus->placa }}</td>
                            <td class="px-4 py-3">{{ $asiento->bus->cooperativa->nombre }}</td>
                            <td class="px-4 py-3">{{ $asiento->tipoAsiento->nombre }}</td>
                            <td class="px-4 py-3">
                                @include('partials.status-badge', ['active' => $asiento->activo])
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('asientos.show', $asiento) }}" class="rounded border border-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-white">Ver</a>
                                    <a href="{{ route('asientos.edit', $asiento) }}" class="rounded border border-blue-200 px-2.5 py-1 text-xs font-semibold text-blue-700 hover:bg-blue-50">Editar</a>
                                    <form method="POST" action="{{ route('asientos.destroy', $asiento) }}" onsubmit="return confirm('Desea eliminar este asiento?')">
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
                                Todavia no hay asientos registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="mt-5">
        {{ $asientos->links() }}
    </div>
@endsection
