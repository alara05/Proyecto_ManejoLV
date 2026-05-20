@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-sm font-medium text-slate-500">Venta interna de oficinista</p>
            <h1 class="text-2xl font-semibold">Boletos vendidos</h1>
        </div>
        <a href="{{ route('boletos.create') }}" class="rounded bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
            Vender boleto
        </a>
    </div>

    @include('partials.flash')

    <section class="overflow-hidden rounded bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] border-collapse text-left text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Codigo</th>
                        <th class="px-4 py-3 font-semibold">Pasajero</th>
                        <th class="px-4 py-3 font-semibold">Salida</th>
                        <th class="px-4 py-3 font-semibold">Asiento</th>
                        <th class="px-4 py-3 font-semibold">Precio</th>
                        <th class="px-4 py-3 font-semibold">Vendido por</th>
                        <th class="px-4 py-3 font-semibold">Estado</th>
                        <th class="px-4 py-3 font-semibold">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($boletos as $boleto)
                        <tr class="align-top hover:bg-slate-50/70">
                            <td class="px-4 py-3 font-semibold text-slate-900">{{ $boleto->codigo }}</td>
                            <td class="px-4 py-3">
                                <p>{{ $boleto->pasajero_nombre }}</p>
                                <p class="text-xs text-slate-500">{{ $boleto->pasajero_cedula }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <p>{{ $boleto->salida->frecuencia->origen->nombre }} a {{ $boleto->salida->frecuencia->destino->nombre }}</p>
                                <p class="text-xs text-slate-500">{{ $boleto->salida->fecha->format('d/m/Y') }} {{ \Illuminate\Support\Str::of($boleto->salida->hora_salida)->substr(0, 5) }}</p>
                            </td>
                            <td class="px-4 py-3">{{ $boleto->asiento->numero }} / {{ $boleto->asiento->tipoAsiento->nombre }}</td>
                            <td class="px-4 py-3">${{ number_format($boleto->precio, 2) }}</td>
                            <td class="px-4 py-3">{{ $boleto->vendedor->name ?? 'Sin registrar' }}</td>
                            <td class="px-4 py-3">{{ ucfirst($boleto->estado) }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('boletos.show', $boleto) }}" class="rounded border border-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-white">Ver</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-10 text-center text-slate-500">
                                Todavia no hay boletos vendidos.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="mt-5">
        {{ $boletos->links() }}
    </div>
@endsection
