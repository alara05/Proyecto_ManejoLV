@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-sm font-medium text-slate-500">Revision de pago</p>
            <h1 class="text-2xl font-semibold">{{ $pago->boleto->codigo }}</h1>
        </div>
        <a href="{{ route('pagos.index') }}" class="rounded border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
            Volver
        </a>
    </div>

    @include('partials.flash')
    @include('partials.validation-errors')

    <section class="rounded bg-white p-6 shadow-sm">
        <div class="grid gap-4 text-sm md:grid-cols-2">
            <div>
                <p class="font-semibold text-slate-500">Pasajero</p>
                <p class="mt-1 text-slate-900">{{ $pago->boleto->pasajero_nombre }}</p>
                <p class="text-xs text-slate-500">{{ $pago->boleto->pasajero_cedula }}</p>
            </div>
            <div>
                <p class="font-semibold text-slate-500">Ruta</p>
                <p class="mt-1 text-slate-900">{{ $pago->boleto->salida->frecuencia->origen->nombre }} a {{ $pago->boleto->salida->frecuencia->destino->nombre }}</p>
                <p class="text-xs text-slate-500">{{ $pago->boleto->salida->fecha->format('d/m/Y') }} {{ \Illuminate\Support\Str::of($pago->boleto->salida->hora_salida)->substr(0, 5) }}</p>
            </div>
            <div>
                <p class="font-semibold text-slate-500">Metodo</p>
                <p class="mt-1 text-slate-900">{{ ucfirst($pago->metodo) }}</p>
            </div>
            <div>
                <p class="font-semibold text-slate-500">Monto</p>
                <p class="mt-1 text-slate-900">${{ number_format($pago->monto, 2) }}</p>
            </div>
            <div>
                <p class="font-semibold text-slate-500">Estado pago</p>
                <p class="mt-1 text-slate-900">{{ ucfirst($pago->estado) }}</p>
            </div>
            <div>
                <p class="font-semibold text-slate-500">Estado boleto</p>
                <p class="mt-1 text-slate-900">{{ ucfirst($pago->boleto->estado) }}</p>
            </div>
            <div>
                <p class="font-semibold text-slate-500">Comprobante</p>
                @if ($pago->comprobante_path)
                    <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($pago->comprobante_path) }}" target="_blank" class="mt-1 inline-flex rounded border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        Abrir comprobante
                    </a>
                @else
                    <p class="mt-1 text-slate-900">Sin comprobante</p>
                @endif
            </div>
            <div>
                <p class="font-semibold text-slate-500">Observacion</p>
                <p class="mt-1 text-slate-900">{{ $pago->observacion ?: 'Sin observacion' }}</p>
            </div>
        </div>

        @if ($pago->estado === 'pendiente')
            <div class="mt-6 flex flex-wrap gap-3">
                <form method="POST" action="{{ route('pagos.validar', $pago) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="rounded bg-emerald-700 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-600">
                        Validar pago
                    </button>
                </form>

                <form method="POST" action="{{ route('pagos.rechazar', $pago) }}" class="flex flex-wrap gap-2">
                    @csrf
                    @method('PATCH')
                    <input name="observacion" type="text" placeholder="Motivo de rechazo"
                        class="rounded border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
                    <button type="submit" class="rounded border border-red-200 px-4 py-2 text-sm font-semibold text-red-700 hover:bg-red-50">
                        Rechazar
                    </button>
                </form>
            </div>
        @endif
    </section>
@endsection
