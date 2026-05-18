@extends('layouts.principal')

@section('title', 'Inicio | ' . config('app.name', 'Manejo Buses'))

@section('content')
<section class="hero" id="inicio">
    <button class="hero-arrow hero-arrow-left" type="button" aria-label="Ver seccion anterior" data-hero-prev>&lsaquo;</button>

    <div class="hero-grid" data-hero-grid>
        <article class="hero-card hero-card-one" style="--hero-image: url('{{ asset('images/inicio/panel-boletos.jpg') }}')">
            <div class="hero-overlay"></div>
            <div class="hero-content">
                <span class="hero-kicker">Sistema de pasajes</span>
                <h1>Viaja en bus por Ecuador</h1>
                <p>Busca rutas interprovinciales, revisa salidas disponibles y prepara tu compra de boletos en un solo lugar.</p>
                <a href="{{ route('register') }}" class="hero-link">Crear cuenta</a>
            </div>
        </article>

        <article class="hero-card hero-card-two" style="--hero-image: url('{{ asset('images/inicio/panel-rutas.jpg') }}')">
            <div class="hero-overlay"></div>
            <div class="hero-content">
                <span class="hero-kicker">Rutas y cooperativas</span>
                <h2>Encuentra tu salida</h2>
                <p>Consulta cooperativas, ciudades de origen y destino, horarios y frecuencias con paradas intermedias.</p>
                <a href="#rutas" class="hero-link">Ver rutas</a>
            </div>
        </article>

        <article class="hero-card hero-card-three" style="--hero-image: url('{{ asset('images/inicio/panel-control.jpg') }}')">
            <div class="hero-overlay"></div>
            <div class="hero-content">
                <span class="hero-kicker">Panel seguro</span>
                <h2>Gestiona buses y ventas</h2>
                <p>Accede con tus credenciales para administrar cooperativas, buses, asientos, boletos y pagos.</p>
                <a href="{{ route('login') }}" class="hero-link">Iniciar sesion</a>
            </div>
        </article>
    </div>

    <button class="hero-arrow hero-arrow-right" type="button" aria-label="Ver seccion siguiente" data-hero-next>&rsaquo;</button>
</section>

<section class="intro-section" id="servicios">
    <div class="section-heading">
        <span>Gestion de transporte</span>
        <h2>Pagina principal para usuarios y cooperativas</h2>
        <p>La portada presenta el sistema de pasajes y mantiene el acceso al login separado para usuarios registrados.</p>
    </div>

    <div class="feature-grid">
        <article class="feature-card">
            <div class="feature-icon">01</div>
            <h3>Busqueda de viajes</h3>
            <p>Filtra por ciudad, cooperativa, tipo de asiento y disponibilidad para elegir la mejor salida.</p>
        </article>

        <article class="feature-card" id="rutas">
            <div class="feature-icon">02</div>
            <h3>Rutas y frecuencias</h3>
            <p>Visualiza origen, destino, horarios, buses asignados y paradas intermedias de cada viaje.</p>
        </article>

        <article class="feature-card" id="beneficios">
            <div class="feature-icon">03</div>
            <h3>Acceso al sistema</h3>
            <p>Menu con registro, inicio de sesion y acceso al panel cuando el usuario ya esta autenticado.</p>
        </article>
    </div>
</section>
@endsection
