@extends('layouts.principal')

@section('title', 'Boleto reservado | ' . config('app.name', 'Manejo Buses'))

@section('content')
<section class="client-ticket-page">
    <div class="client-ticket-shell">
        @if (session('success'))
            <div class="client-alert client-alert-success">{{ session('success') }}</div>
        @endif

        <div class="client-ticket-heading">
            <span>Confirmacion</span>
            <h1>Boleto {{ $boleto->codigo }}</h1>
        </div>

        <section class="client-ticket-panel">
            <div class="client-trip-summary">
                <div>
                    <span>Pasajero</span>
                    <strong>{{ $boleto->pasajero_nombre }}</strong>
                    <small>{{ $boleto->pasajero_cedula }}</small>
                </div>
                <div>
                    <span>Origen / destino</span>
                    <strong>{{ $boleto->salida->frecuencia->origen->nombre }} a {{ $boleto->salida->frecuencia->destino->nombre }}</strong>
                    <small>{{ $boleto->salida->fecha->format('d/m/Y') }} {{ \Illuminate\Support\Str::of($boleto->salida->hora_salida)->substr(0, 5) }}</small>
                </div>
                <div>
                    <span>Asiento</span>
                    <strong>{{ $boleto->asiento->numero }} / {{ $boleto->asiento->tipoAsiento->nombre }}</strong>
                    <small>Bus {{ $boleto->salida->bus->numero }} - {{ $boleto->salida->bus->placa }}</small>
                </div>
                <div>
                    <span>Precio final</span>
                    <strong>${{ number_format($boleto->precio, 2) }}</strong>
                    <small>{{ ucfirst(str_replace('_', ' ', $boleto->tipo_descuento)) }} / {{ number_format($boleto->porcentaje_descuento, 0) }}%</small>
                </div>
                <div>
                    <span>Estado</span>
                    <strong>{{ ucfirst($boleto->estado) }}</strong>
                    <small>Pago: {{ $boleto->pago ? ucfirst($boleto->pago->estado) : 'Sin comprobante' }}</small>
                </div>
            </div>

            <div class="client-ticket-actions client-ticket-actions-single">
                <a href="{{ route('cliente.boletos.pdf', $boleto) }}">Descargar boleto PDF</a>
                <a href="{{ route('cliente.pagos.create', $boleto) }}">Subir comprobante</a>
                <a href="{{ route('cliente.boletos.create') }}">Comprar otro boleto</a>
            </div>
        </section>
    </div>
</section>
@endsection
