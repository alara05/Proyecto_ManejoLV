@extends('layouts.principal')

@section('title', 'Historial de compras | Cuchao')

@section('content')
<section class="client-ticket-page">
    <div class="client-ticket-shell">
        <div class="interface-hero interface-hero-history" style="--page-image: url('{{ asset('images/inicio/panel-control.jpg') }}')">
            <div class="interface-hero-copy">
                <span class="hero-kicker">Historial de compras</span>
                <h1>Mis boletos</h1>
                <p>Consulta tus compras, estados de pago y boletos descargables desde una vista clara y conectada al inicio.</p>
            </div>
            <div class="interface-hero-panel">
                <span>Compras</span>
                <strong>{{ $boletos->total() }}</strong>
                <small>boleto{{ $boletos->total() === 1 ? '' : 's' }} registrado{{ $boletos->total() === 1 ? '' : 's' }}</small>
            </div>
        </div>

        <section class="client-ticket-panel">
            <div class="client-history-list">
                @forelse ($boletos as $boleto)
                    <article class="client-history-item">
                        <div class="client-history-main">
                            <span>{{ $boleto->codigo }}</span>
                            <strong>{{ $boleto->salida->frecuencia->origen->nombre }} a {{ $boleto->salida->frecuencia->destino->nombre }}</strong>
                            <small>
                                {{ $boleto->salida->fecha->format('d/m/Y') }}
                                {{ \Illuminate\Support\Str::of($boleto->salida->hora_salida)->substr(0, 5) }}
                                / Bus {{ $boleto->salida->bus->numero }}
                                / Asiento {{ $boleto->asiento->numero }}
                            </small>
                        </div>

                        <div class="client-history-status-grid">
                            <div>
                                <span>Boleto</span>
                                <strong>{{ ucfirst($boleto->estado) }}</strong>
                                <small>${{ number_format($boleto->precio, 2) }}</small>
                            </div>
                            <div>
                                <span>Pago</span>
                                <strong>{{ $boleto->pago ? ucfirst($boleto->pago->estado) : 'Pendiente' }}</strong>
                                <small>
                                    @if ($boleto->pago)
                                        {{ ucfirst($boleto->pago->metodo) }} / ${{ number_format($boleto->pago->monto, 2) }}
                                    @else
                                        Sin comprobante
                                    @endif
                                </small>
                            </div>
                        </div>

                        <div class="client-history-actions">
                            <a href="{{ route('cliente.boletos.show', $boleto) }}">Ver</a>
                            @if (! $boleto->pago || $boleto->pago->estado === 'rechazado')
                                <a href="{{ route('cliente.pagos.create', $boleto) }}">Pagar</a>
                            @endif
                            <a href="{{ route('cliente.boletos.pdf', $boleto) }}">PDF</a>
                        </div>
                    </article>
                @empty
                    <p class="client-empty-message">Todavia no tienes boletos registrados en tu cuenta.</p>
                @endforelse
            </div>

            <div class="travel-pagination">
                {{ $boletos->links() }}
            </div>
        </section>
    </div>
</section>
@endsection
