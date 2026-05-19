@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <p class="text-sm font-medium text-slate-500">Personal del bus</p>
        <h1 class="text-2xl font-semibold">Registro de acceso de pasajeros</h1>
    </div>

    @include('partials.flash')
    @include('partials.validation-errors')

    <section class="mb-6 rounded bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('accesos.store') }}" class="grid gap-4 md:grid-cols-[1fr_auto] md:items-end">
            @csrf
            <div>
                <label for="codigo" class="block text-sm font-semibold text-slate-700">Codigo del boleto</label>
                <input id="codigo" name="codigo" type="text" value="{{ old('codigo') }}" required autofocus
                    placeholder="BOL-20260519-ABC123"
                    class="mt-2 w-full rounded border border-slate-300 px-3 py-2 text-sm uppercase focus:border-slate-900 focus:outline-none">
            </div>
            <button type="submit" class="rounded bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
                Validar acceso
            </button>
        </form>
    </section>

    <section class="overflow-hidden rounded bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[920px] border-collapse text-left text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Fecha</th>
                        <th class="px-4 py-3 font-semibold">Codigo</th>
                        <th class="px-4 py-3 font-semibold">Pasajero</th>
                        <th class="px-4 py-3 font-semibold">Viaje</th>
                        <th class="px-4 py-3 font-semibold">Asiento</th>
                        <th class="px-4 py-3 font-semibold">Resultado</th>
                        <th class="px-4 py-3 font-semibold">Registrado por</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($registros as $registro)
                        <tr class="align-top hover:bg-slate-50/70">
                            <td class="px-4 py-3">{{ $registro->registrado_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-900">{{ $registro->boleto->codigo }}</td>
                            <td class="px-4 py-3">
                                <p>{{ $registro->boleto->pasajero_nombre }}</p>
                                <p class="text-xs text-slate-500">{{ $registro->boleto->pasajero_cedula }}</p>
                            </td>
                            <td class="px-4 py-3">
                                {{ $registro->boleto->salida->frecuencia->origen->nombre }}
                                a
                                {{ $registro->boleto->salida->frecuencia->destino->nombre }}
                            </td>
                            <td class="px-4 py-3">{{ $registro->boleto->asiento->numero }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded px-2.5 py-1 text-xs font-semibold {{ $registro->resultado === 'permitido' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($registro->resultado) }}
                                </span>
                                <p class="mt-1 text-xs text-slate-500">{{ $registro->observacion }}</p>
                            </td>
                            <td class="px-4 py-3">{{ $registro->registrador->name ?? 'Sin registrar' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-slate-500">
                                Todavia no hay accesos registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="mt-5">
        {{ $registros->links() }}
    </div>
@endsection
