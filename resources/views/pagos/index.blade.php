@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <p class="text-sm font-medium text-slate-500">Validacion manual</p>
        <h1 class="text-2xl font-semibold">Pagos con comprobante</h1>
    </div>

    @include('partials.flash')

    <section class="overflow-hidden rounded bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] border-collapse text-left text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Boleto</th>
                        <th class="px-4 py-3 font-semibold">Pasajero</th>
                        <th class="px-4 py-3 font-semibold">Metodo</th>
                        <th class="px-4 py-3 font-semibold">Monto</th>
                        <th class="px-4 py-3 font-semibold">Estado</th>
                        <th class="px-4 py-3 font-semibold">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($pagos as $pago)
                        <tr class="align-top hover:bg-slate-50/70">
                            <td class="px-4 py-3">
                                <p class="font-semibold text-slate-900">{{ $pago->boleto->codigo }}</p>
                                <p class="text-xs text-slate-500">{{ $pago->boleto->salida->frecuencia->origen->nombre }} a {{ $pago->boleto->salida->frecuencia->destino->nombre }}</p>
                            </td>
                            <td class="px-4 py-3">{{ $pago->boleto->pasajero_nombre }}</td>
                            <td class="px-4 py-3">{{ ucfirst($pago->metodo) }}</td>
                            <td class="px-4 py-3">${{ number_format($pago->monto, 2) }}</td>
                            <td class="px-4 py-3">{{ ucfirst($pago->estado) }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('pagos.show', $pago) }}" class="rounded border border-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-white">Revisar</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-slate-500">Todavia no hay pagos registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="mt-5">
        {{ $pagos->links() }}
    </div>
@endsection
