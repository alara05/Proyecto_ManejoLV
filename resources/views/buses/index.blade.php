@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-sm font-medium text-slate-500">Gestion de flota</p>
            <h1 class="text-2xl font-semibold">Buses</h1>
        </div>
        <a href="{{ route('buses.create') }}" class="rounded bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">
            Nuevo bus
        </a>
    </div>

    @if (session('success'))
        <div class="mb-5 rounded border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <section class="overflow-hidden rounded bg-white shadow-sm">
        <table class="w-full border-collapse text-left text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="px-4 py-3 font-medium">Numero</th>
                    <th class="px-4 py-3 font-medium">Cooperativa</th>
                    <th class="px-4 py-3 font-medium">Placa</th>
                    <th class="px-4 py-3 font-medium">Chasis</th>
                    <th class="px-4 py-3 font-medium">Capacidad</th>
                    <th class="px-4 py-3 font-medium">Estado</th>
                    <th class="px-4 py-3 font-medium">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($buses as $bus)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $bus->numero }}</td>
                        <td class="px-4 py-3">{{ $bus->cooperativa->nombre }}</td>
                        <td class="px-4 py-3">{{ $bus->placa }}</td>
                        <td class="px-4 py-3">{{ $bus->marca_chasis ?? 'Sin registrar' }}</td>
                        <td class="px-4 py-3">{{ $bus->capacidad_total }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded px-2 py-1 text-xs font-medium {{ $bus->activo ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600' }}">
                                {{ $bus->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('buses.show', $bus) }}" class="text-slate-700 hover:text-slate-950">Ver</a>
                                <a href="{{ route('buses.edit', $bus) }}" class="text-blue-700 hover:text-blue-900">Editar</a>
                                <form method="POST" action="{{ route('buses.destroy', $bus) }}" onsubmit="return confirm('Desea eliminar este bus?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-slate-500">
                            Todavia no hay buses registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>

    <div class="mt-5">
        {{ $buses->links() }}
    </div>
@endsection
