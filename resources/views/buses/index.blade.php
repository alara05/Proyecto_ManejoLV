@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-sm font-medium text-slate-500">Gestion de flota</p>
            <h1 class="text-2xl font-semibold">Buses</h1>
        </div>
        <a href="{{ route('buses.create') }}" class="rounded bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
            Nuevo bus
        </a>
    </div>

    @include('partials.flash')

    <section class="overflow-hidden rounded bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[760px] border-collapse text-left text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Numero</th>
                        <th class="px-4 py-3 font-semibold">Cooperativa</th>
                        <th class="px-4 py-3 font-semibold">Placa</th>
                        <th class="px-4 py-3 font-semibold">Chasis</th>
                        <th class="px-4 py-3 font-semibold">Capacidad</th>
                        <th class="px-4 py-3 font-semibold">Estado</th>
                        <th class="px-4 py-3 font-semibold">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($buses as $bus)
                        <tr class="align-top hover:bg-slate-50/70">
                            <td class="px-4 py-3 font-semibold text-slate-900">Bus {{ $bus->numero }}</td>
                            <td class="px-4 py-3">{{ $bus->cooperativa->nombre }}</td>
                            <td class="px-4 py-3">{{ $bus->placa }}</td>
                            <td class="px-4 py-3">{{ $bus->marca_chasis ?? 'Sin registrar' }}</td>
                            <td class="px-4 py-3">{{ $bus->capacidad_total }}</td>
                            <td class="px-4 py-3">
                                @include('partials.status-badge', ['active' => $bus->activo])
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('buses.show', $bus) }}" class="rounded border border-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-white">Ver</a>
                                    <a href="{{ route('buses.edit', $bus) }}" class="rounded border border-blue-200 px-2.5 py-1 text-xs font-semibold text-blue-700 hover:bg-blue-50">Editar</a>
                                    <form method="POST" action="{{ route('buses.destroy', $bus) }}" onsubmit="return confirm('Desea eliminar este bus?')">
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
                                Todavia no hay buses registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="mt-5">
        {{ $buses->links() }}
    </div>
@endsection
