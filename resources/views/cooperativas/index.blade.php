@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-sm font-medium text-slate-500">Administracion</p>
            <h1 class="text-2xl font-semibold">Cooperativas</h1>
        </div>
        <a href="{{ route('cooperativas.create') }}" class="rounded bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
            Nueva cooperativa
        </a>
    </div>

    @include('partials.flash')

    <section class="overflow-hidden rounded bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[760px] border-collapse text-left text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Nombre</th>
                        <th class="px-4 py-3 font-semibold">RUC</th>
                        <th class="px-4 py-3 font-semibold">Contacto</th>
                        <th class="px-4 py-3 font-semibold">Buses</th>
                        <th class="px-4 py-3 font-semibold">Rutas</th>
                        <th class="px-4 py-3 font-semibold">Estado</th>
                        <th class="px-4 py-3 font-semibold">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($cooperativas as $cooperativa)
                        <tr class="align-top hover:bg-slate-50/70">
                            <td class="px-4 py-3">
                                <p class="font-semibold text-slate-900">{{ $cooperativa->nombre }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $cooperativa->direccion ?? 'Direccion sin registrar' }}</p>
                            </td>
                            <td class="px-4 py-3">{{ $cooperativa->ruc ?? 'Sin registrar' }}</td>
                            <td class="px-4 py-3">
                                <p>{{ $cooperativa->telefono ?? 'Sin telefono' }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $cooperativa->email ?? 'Sin correo' }}</p>
                            </td>
                            <td class="px-4 py-3">{{ $cooperativa->buses_count }}</td>
                            <td class="px-4 py-3">{{ $cooperativa->rutas_count }}</td>
                            <td class="px-4 py-3">
                                @include('partials.status-badge', ['active' => $cooperativa->activa, 'trueLabel' => 'Activa', 'falseLabel' => 'Inactiva'])
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('cooperativas.show', $cooperativa) }}" class="rounded border border-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-white">Ver</a>
                                    <a href="{{ route('cooperativas.edit', $cooperativa) }}" class="rounded border border-blue-200 px-2.5 py-1 text-xs font-semibold text-blue-700 hover:bg-blue-50">Editar</a>
                                    <form method="POST" action="{{ route('cooperativas.destroy', $cooperativa) }}" onsubmit="return confirm('Desea eliminar esta cooperativa?')">
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
                                Todavia no hay cooperativas registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="mt-5">
        {{ $cooperativas->links() }}
    </div>
@endsection
