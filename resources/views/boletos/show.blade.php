@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-sm font-medium text-slate-500">Detalle de boleto</p>
            <h1 class="text-2xl font-semibold">{{ $boleto->codigo }}</h1>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('cliente.boletos.pdf', $boleto) }}" class="rounded bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
                Descargar PDF
            </a>
            <a href="{{ route('boletos.index') }}" class="rounded border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Volver
            </a>
        </div>
    </div>

    @include('partials.flash')

    <section class="rounded bg-white p-6 shadow-sm">
        <div class="grid gap-4 text-sm md:grid-cols-2">
            <div>
                <p class="font-semibold text-slate-500">Pasajero</p>
                <p class="mt-1 text-slate-900">{{ $boleto->pasajero_nombre }}</p>
                <p class="text-xs text-slate-500">{{ $boleto->pasajero_cedula }}</p>
            </div>
            <div>
                <p class="font-semibold text-slate-500">Salida</p>
                <p class="mt-1 text-slate-900">
                    {{ $boleto->salida->frecuencia->origen->nombre }} a {{ $boleto->salida->frecuencia->destino->nombre }}
                </p>
                <p class="text-xs text-slate-500">
                    {{ $boleto->salida->fecha->format('d/m/Y') }} {{ \Illuminate\Support\Str::of($boleto->salida->hora_salida)->substr(0, 5) }}
                </p>
            </div>
            <div>
                <p class="font-semibold text-slate-500">Bus</p>
                <p class="mt-1 text-slate-900">Bus {{ $boleto->salida->bus->numero }} - {{ $boleto->salida->bus->placa }}</p>
            </div>
            <div>
                <p class="font-semibold text-slate-500">Asiento</p>
                <p class="mt-1 text-slate-900">{{ $boleto->asiento->numero }} / {{ $boleto->asiento->tipoAsiento->nombre }}</p>
            </div>
            <div>
                <p class="font-semibold text-slate-500">Precio</p>
                <p class="mt-1 text-slate-900">${{ number_format($boleto->precio, 2) }}</p>
            </div>
            <div>
                <p class="font-semibold text-slate-500">Descuento</p>
                <p class="mt-1 text-slate-900">
                    {{ str_replace('_', ' ', ucfirst($boleto->tipo_descuento)) }} / {{ number_format($boleto->porcentaje_descuento, 0) }}%
                </p>
            </div>
            <div>
                <p class="font-semibold text-slate-500">Vendido por</p>
                <p class="mt-1 text-slate-900">{{ $boleto->vendedor->name ?? 'Sin registrar' }}</p>
            </div>
            <div>
                <p class="font-semibold text-slate-500">Estado</p>
                <p class="mt-1 text-slate-900">{{ ucfirst($boleto->estado) }}</p>
            </div>
        </div>
    </section>
@endsection
