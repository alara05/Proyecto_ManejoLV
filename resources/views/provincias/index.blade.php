@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-sm font-medium text-slate-500">Catalogo geografico</p>
            <h1 class="text-2xl font-semibold">Provincias</h1>
        </div>
        <a href="{{ route('provincias.create') }}" class="rounded bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
            Nueva provincia
        </a>
    </div>

    @include('partials.flash')

    <section class="overflow-hidden rounded bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[640px] border-collapse text-left text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Nombre</th>
                        <th class="px-4 py-3 font-semibold">Ciudades</th>
                        <th class="px-4 py-3 font-semibold">Estado</th>
                        <th class="px-4 py-3 font-semibold">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($provincias as $provincia)
                        <tr class="align-top hover:bg-slate-50/70">
                            <td class="px-4 py-3 font-semibold text-slate-900">{{ $provincia->nombre }}</td>
                            <td class="px-4 py-3">{{ $provincia->ciudades_count }}</td>
                            <td class="px-4 py-3">
                                @include('partials.status-badge', ['active' => $provincia->activa, 'trueLabel' => 'Activa', 'falseLabel' => 'Inactiva'])
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('provincias.show', $provincia) }}" class="rounded border border-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-white">Ver</a>
                                    <a href="{{ route('provincias.edit', $provincia) }}" class="rounded border border-blue-200 px-2.5 py-1 text-xs font-semibold text-blue-700 hover:bg-blue-50">Editar</a>
                                    <form method="POST" action="{{ route('provincias.destroy', $provincia) }}" onsubmit="return confirm('Desea eliminar esta provincia?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded border border-red-200 px-2.5 py-1 text-xs font-semibold text-red-700 hover:bg-red-50">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-slate-500">
                                Todavia no hay provincias registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="mt-5">
        {{ $provincias->links() }}
    </div>
@endsection
