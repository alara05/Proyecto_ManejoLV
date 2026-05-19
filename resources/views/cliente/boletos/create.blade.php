@extends('layouts.principal')

@section('title', 'Comprar boleto | ' . config('app.name', 'Manejo Buses'))

@section('content')
<section class="client-ticket-page">
    <div class="client-ticket-shell">
        <div class="client-ticket-heading">
            <span>Compra de boletos</span>
            <h1>Elige tu salida y asiento</h1>
        </div>

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

        <form method="GET" action="{{ route('cliente.boletos.create') }}" class="client-ticket-panel">
            <div class="client-ticket-grid">
                <label class="client-ticket-field client-ticket-wide">
                    <span>Salida</span>
                    <select name="salida_id" required>
                        <option value="">Seleccione una salida</option>
                        @foreach ($salidas as $salidaOption)
                            <option value="{{ $salidaOption->id }}" @selected(request('salida_id') == $salidaOption->id)>
                                {{ $salidaOption->fecha->format('d/m/Y') }}
                                {{ \Illuminate\Support\Str::of($salidaOption->hora_salida)->substr(0, 5) }}
                                / {{ $salidaOption->frecuencia->origen->nombre }} - {{ $salidaOption->frecuencia->destino->nombre }}
                                / Bus {{ $salidaOption->bus->numero }}
                            </option>
                        @endforeach
                    </select>
                </label>

                <label class="client-ticket-field">
                    <span>Tipo de asiento</span>
                    <select name="tipo_asiento_id">
                        <option value="">Todos</option>
                        @foreach ($tiposAsiento as $tipoAsiento)
                            <option value="{{ $tipoAsiento->id }}" @selected($selectedTipoAsientoId == $tipoAsiento->id)>
                                {{ $tipoAsiento->nombre }}{{ $tipoAsiento->recargo > 0 ? ' + $' . number_format($tipoAsiento->recargo, 2) : '' }}
                            </option>
                        @endforeach
                    </select>
                </label>

                <div class="client-ticket-actions">
                    <button type="submit">Ver asientos</button>
                    <a href="{{ route('cliente.boletos.create') }}">Limpiar</a>
                </div>
            </div>
        </form>

        @if ($salida)
            <form method="POST" action="{{ route('cliente.boletos.store') }}" class="client-ticket-panel">
                @csrf

                <input type="hidden" name="salida_id" value="{{ $salida->id }}">
                <input type="hidden" name="tipo_asiento_id" value="{{ $selectedTipoAsientoId }}">

                <div class="client-trip-summary">
                    <div>
                        <span>Viaje</span>
                        <strong>{{ $salida->frecuencia->origen->nombre }} a {{ $salida->frecuencia->destino->nombre }}</strong>
                    </div>
                    <div>
                        <span>Fecha y hora</span>
                        <strong>{{ $salida->fecha->format('d/m/Y') }} {{ \Illuminate\Support\Str::of($salida->hora_salida)->substr(0, 5) }}</strong>
                    </div>
                    <div>
                        <span>Precio base</span>
                        <strong>${{ number_format($salida->precio_base, 2) }}</strong>
                    </div>
                </div>

                <div class="client-seat-section">
                    <h2>Asientos</h2>
                    <div class="client-seat-grid">
                        @forelse ($asientos as $asiento)
                            @php
                                $ocupado = $asientosOcupados->contains($asiento->id);
                                $subtotal = (float) $salida->precio_base + (float) $asiento->tipoAsiento->recargo;
                            @endphp
                            <label class="client-seat-option {{ $ocupado ? 'is-busy' : '' }}">
                                <input type="radio" name="asiento_id" value="{{ $asiento->id }}" @disabled($ocupado) @checked(old('asiento_id') == $asiento->id)>
                                <strong>{{ $asiento->numero }}</strong>
                                <span>{{ $asiento->tipoAsiento->nombre }}</span>
                                <small>${{ number_format($subtotal, 2) }} {{ $ocupado ? '/ Ocupado' : '/ Disponible' }}</small>
                            </label>
                        @empty
                            <p class="client-empty-message">No hay asientos activos para el filtro seleccionado.</p>
                        @endforelse
                    </div>
                </div>

                <div class="client-ticket-grid">
                    <label class="client-ticket-field">
                        <span>Pasajero</span>
                        <input name="pasajero_nombre" type="text" value="{{ old('pasajero_nombre') }}" required>
                    </label>

                    <label class="client-ticket-field">
                        <span>Cedula</span>
                        <input name="pasajero_cedula" type="text" value="{{ old('pasajero_cedula') }}" maxlength="10" required>
                    </label>

                    <label class="client-ticket-field">
                        <span>Correo de contacto</span>
                        <input name="cliente_email" type="email" value="{{ old('cliente_email', auth()->user()?->email) }}" @guest required @endguest>
                    </label>

                    <label class="client-ticket-field">
                        <span>Descuento</span>
                        <select name="tipo_descuento" required>
                            @foreach ($descuentos as $value => $label)
                                <option value="{{ $value }}" @selected(old('tipo_descuento', 'ninguno') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </label>

                    <div class="client-ticket-actions">
                        <button type="submit">Reservar boleto</button>
                    </div>
                </div>
            </form>
        @endif
    </div>
</section>
@endsection
