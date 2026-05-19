@extends('layouts.principal')

@section('title', 'Buscar viajes | ' . config('app.name', 'Manejo Buses'))

@section('content')
<section class="travel-search-page">
    <div class="travel-search-shell">
        <div class="travel-search-heading">
            <span>Viajes disponibles</span>
            <h1>Buscar destinos y frecuencias</h1>
        </div>

        <form method="GET" action="{{ route('viajes.buscar') }}" class="travel-filter-panel">
            <div class="travel-filter-grid">
                <label class="travel-field">
                    <span>Origen</span>
                    <select name="ciudad_origen_id">
                        <option value="">Todos</option>
                        @foreach ($ciudades as $ciudad)
                            <option value="{{ $ciudad->id }}" @selected(($filters['ciudad_origen_id'] ?? '') == $ciudad->id)>
                                {{ $ciudad->nombre }} - {{ $ciudad->provincia->nombre }}
                            </option>
                        @endforeach
                    </select>
                </label>

                <label class="travel-field">
                    <span>Destino</span>
                    <select name="ciudad_destino_id">
                        <option value="">Todos</option>
                        @foreach ($ciudades as $ciudad)
                            <option value="{{ $ciudad->id }}" @selected(($filters['ciudad_destino_id'] ?? '') == $ciudad->id)>
                                {{ $ciudad->nombre }} - {{ $ciudad->provincia->nombre }}
                            </option>
                        @endforeach
                    </select>
                </label>

                <label class="travel-field">
                    <span>Cooperativa</span>
                    <select name="cooperativa_id">
                        <option value="">Todas</option>
                        @foreach ($cooperativas as $cooperativa)
                            <option value="{{ $cooperativa->id }}" @selected(($filters['cooperativa_id'] ?? '') == $cooperativa->id)>
                                {{ $cooperativa->nombre }}
                            </option>
                        @endforeach
                    </select>
                </label>

                <label class="travel-field">
                    <span>Tipo de asiento</span>
                    <select name="tipo_asiento_id">
                        <option value="">Todos</option>
                        @foreach ($tiposAsiento as $tipoAsiento)
                            <option value="{{ $tipoAsiento->id }}" @selected(($filters['tipo_asiento_id'] ?? '') == $tipoAsiento->id)>
                                {{ $tipoAsiento->nombre }}
                            </option>
                        @endforeach
                    </select>
                </label>

                <label class="travel-field">
                    <span>Chasis</span>
                    <select name="marca_chasis">
                        <option value="">Todos</option>
                        @foreach ($marcasChasis as $marcaChasis)
                            <option value="{{ $marcaChasis }}" @selected(($filters['marca_chasis'] ?? '') === $marcaChasis)>
                                {{ $marcaChasis }}
                            </option>
                        @endforeach
                    </select>
                </label>

                <label class="travel-field">
                    <span>Carroceria</span>
                    <select name="marca_carroceria">
                        <option value="">Todas</option>
                        @foreach ($marcasCarroceria as $marcaCarroceria)
                            <option value="{{ $marcaCarroceria }}" @selected(($filters['marca_carroceria'] ?? '') === $marcaCarroceria)>
                                {{ $marcaCarroceria }}
                            </option>
                        @endforeach
                    </select>
                </label>

                <label class="travel-field">
                    <span>Tipo de viaje</span>
                    <select name="tipo_viaje">
                        <option value="">Todos</option>
                        <option value="directo" @selected(($filters['tipo_viaje'] ?? '') === 'directo')>Directo</option>
                        <option value="con_paradas" @selected(($filters['tipo_viaje'] ?? '') === 'con_paradas')>Con paradas</option>
                    </select>
                </label>

                <div class="travel-filter-actions">
                    <button type="submit">Buscar</button>
                    <a href="{{ route('viajes.buscar') }}">Limpiar</a>
                </div>
            </div>
        </form>

        <div class="travel-results-head">
            <p>{{ $viajes->total() }} resultado{{ $viajes->total() === 1 ? '' : 's' }}</p>
        </div>

        <div class="travel-results-grid">
            @forelse ($viajes as $viaje)
                @php
                    $frecuencias = $frecuenciasPorRuta[$viaje->id] ?? collect();
                    $tiposDisponibles = $viaje->bus->asientos
                        ->where('activo', true)
                        ->pluck('tipoAsiento.nombre')
                        ->filter()
                        ->unique()
                        ->values();
                @endphp

                <article class="travel-result-card">
                    <div class="travel-card-top">
                        <span>{{ $viaje->tipo_viaje === 'directo' ? 'Directo' : 'Con paradas' }}</span>
                        <strong>{{ $viaje->cooperativa->nombre }}</strong>
                    </div>

                    <h2>{{ $viaje->origen->nombre }} a {{ $viaje->destino->nombre }}</h2>
                    <p class="travel-route-name">{{ $viaje->nombre }}</p>

                    <dl class="travel-details">
                        <div>
                            <dt>Bus</dt>
                            <dd>{{ $viaje->bus->numero }} - {{ $viaje->bus->placa }}</dd>
                        </div>
                        <div>
                            <dt>Chasis</dt>
                            <dd>{{ $viaje->bus->marca_chasis ?: 'Sin registrar' }}</dd>
                        </div>
                        <div>
                            <dt>Carroceria</dt>
                            <dd>{{ $viaje->bus->marca_carroceria ?: 'Sin registrar' }}</dd>
                        </div>
                        <div>
                            <dt>Duracion</dt>
                            <dd>{{ $viaje->duracion_minutos ? $viaje->duracion_minutos . ' min' : 'Sin registrar' }}</dd>
                        </div>
                    </dl>

                    <div class="travel-seat-list">
                        @forelse ($tiposDisponibles as $tipoDisponible)
                            <span>{{ $tipoDisponible }}</span>
                        @empty
                            <span>Sin asientos activos</span>
                        @endforelse
                    </div>

                    <div class="travel-frequency-list">
                        @forelse ($frecuencias as $frecuencia)
                            <span>{{ \Illuminate\Support\Str::of($frecuencia->hora_salida)->substr(0, 5) }}</span>
                        @empty
                            <span>Sin frecuencias</span>
                        @endforelse
                    </div>
                </article>
            @empty
                <div class="travel-empty-state">
                    <h2>Sin viajes disponibles</h2>
                    <p>No hay frecuencias activas con los filtros seleccionados.</p>
                </div>
            @endforelse
        </div>

        <div class="travel-pagination">
            {{ $viajes->links() }}
        </div>
    </div>
</section>
@endsection
