@extends('layouts.principal')

@section('title', 'Subir comprobante | Cuchao')

@section('content')
<section class="client-ticket-page">
    <div class="client-ticket-shell">
        @if (session('success'))
            <div class="client-alert client-alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="client-alert client-alert-error">
                <strong>Revisa los datos ingresados.</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="interface-hero interface-hero-payments" style="--page-image: url('{{ asset('images/inicio/panel-control.jpg') }}')">
            <div class="interface-hero-copy">
                <span class="hero-kicker">Pago de boleto</span>
                <h1>Subir comprobante</h1>
                <p>Carga tu deposito o transferencia para que el equipo valide el pago y emita tu boleto.</p>
            </div>
            <div class="interface-hero-panel">
                <span>Total</span>
                <strong>${{ number_format($boleto->precio, 2) }}</strong>
                <small>{{ $boleto->pago ? 'Pago ' . $boleto->pago->estado : 'pendiente de comprobante' }}</small>
            </div>
        </div>

        <section class="client-ticket-panel">
            <div class="client-trip-summary">
                <div>
                    <span>Boleto</span>
                    <strong>{{ $boleto->codigo }}</strong>
                    <small>{{ $boleto->pasajero_nombre }}</small>
                </div>
                <div>
                    <span>Ruta</span>
                    <strong>{{ $boleto->salida->frecuencia->origen->nombre }} a {{ $boleto->salida->frecuencia->destino->nombre }}</strong>
                    <small>{{ $boleto->salida->fecha->format('d/m/Y') }} {{ \Illuminate\Support\Str::of($boleto->salida->hora_salida)->substr(0, 5) }}</small>
                </div>
                <div>
                    <span>Total</span>
                    <strong>${{ number_format($boleto->precio, 2) }}</strong>
                    <small>Estado boleto: {{ ucfirst($boleto->estado) }}</small>
                </div>
                @if ($boleto->pago)
                    <div>
                        <span>Pago actual</span>
                        <strong>{{ ucfirst($boleto->pago->estado) }}</strong>
                        <small>{{ ucfirst($boleto->pago->metodo) }} / ${{ number_format($boleto->pago->monto, 2) }}</small>
                    </div>
                @endif
            </div>

            <form method="POST" action="{{ route('cliente.pagos.store', $boleto) }}" enctype="multipart/form-data" class="client-ticket-grid">
                @csrf

                <label class="client-ticket-field">
                    <span>Metodo</span>
                    <select name="metodo" required>
                        @foreach ($metodos as $value => $label)
                            <option value="{{ $value }}" @selected(old('metodo', $boleto->pago->metodo ?? 'transferencia') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="client-ticket-field">
                    <span>Monto</span>
                    <input name="monto" type="number" min="0.01" step="0.01" value="{{ old('monto', $boleto->precio) }}" required>
                </label>

                <label class="client-ticket-field">
                    <span>Comprobante</span>
                    <input name="comprobante" type="file" accept=".jpg,.jpeg,.png,.pdf" required>
                </label>

                <label class="client-ticket-field client-ticket-wide">
                    <span>Observacion</span>
                    <input name="observacion" type="text" value="{{ old('observacion', $boleto->pago->observacion ?? '') }}" placeholder="Numero de operacion o referencia">
                </label>

                <div class="client-ticket-actions">
                    <button type="submit">Enviar comprobante</button>
                    <a href="{{ route('cliente.boletos.show', $boleto) }}">Volver</a>
                </div>
            </form>
        </section>
    </div>
</section>
@endsection
