@extends('layouts.principal')

@section('title', 'Historial de boletos | ' . config('app.name', 'Manejo Buses'))

@section('content')
<section class="client-ticket-page">
    <div class="client-ticket-shell">
        <div class="client-ticket-heading">
            <span>Historial</span>
            <h1>Mis boletos</h1>
        </div>

        <section class="client-ticket-panel">
            <div class="client-history-list">
                @forelse ($boletos as $boleto)
                    <article class="client-history-item">
                        <div>
                            <span>{{ $boleto->codigo }}</span>
                            <strong>{{ $boleto->salida->frecuencia->origen->nombre }} a {{ $boleto->salida->frecuencia->destino->nombre }}</strong>
                            <small>
                                {{ $boleto->salida->fecha->format('d/m/Y') }}
                                {{ \Illuminate\Support\Str::of($boleto->salida->hora_salida)->substr(0, 5) }}
                                / Asiento {{ $boleto->asiento->numero }}
                                / {{ ucfirst($boleto->estado) }}
                            </small>
                        </div>
                        <div class="client-history-actions">
                            <a href="{{ route('cliente.boletos.show', $boleto) }}">Ver</a>
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
