@extends('layouts.principal')

@section('title', 'Inicio | ' . config('app.name', 'Sistema de Pasajes'))

@section('content')
<section class="hero" id="inicio">
    <button class="hero-arrow hero-arrow-left" type="button" aria-label="Ver seccion anterior" data-hero-prev>‹</button>

    <div class="hero-grid" data-hero-grid>
        <article class="hero-card hero-card-one" style="--hero-image: url('{{ asset('images/inicio/panel-boletos.jpg') }}')">
            <div class="hero-overlay"></div>
            <div class="hero-content">
                <span class="hero-kicker">Sistema de pasajes</span>
                <h1>Gestiona tus boletos</h1>
                <p>Consulta rutas, salidas y disponibilidad desde una interfaz rapida, moderna y facil de usar.</p>
            </div>
        </article>

        <article class="hero-card hero-card-two" style="--hero-image: url('{{ asset('images/inicio/panel-rutas.jpg') }}')">
            <div class="hero-overlay"></div>
            <div class="hero-content">
                <span class="hero-kicker">Rutas y cooperativas</span>
                <h2>Encuentra tu viaje</h2>
                <p>Organiza la informacion principal de ciudades, frecuencias, buses y cooperativas.</p>
            </div>
        </article>

        <article class="hero-card hero-card-three" style="--hero-image: url('{{ asset('images/inicio/panel-control.jpg') }}')">
            <div class="hero-overlay"></div>
            <div class="hero-content">
                <span class="hero-kicker">Panel seguro</span>
                <h2>Administra el sistema</h2>
                <p>Accede con tus credenciales para controlar usuarios, ventas, buses, asientos y reportes.</p>
            </div>
        </article>
    </div>

    <button class="hero-arrow hero-arrow-right" type="button" aria-label="Ver seccion siguiente" data-hero-next>›</button>
</section>

<section class="intro-section" id="servicios">
    <div class="section-heading">
        <span>Interfaz principal</span>
        <h2>Una pagina de inicio preparada para presentar la aplicacion</h2>
        <p>Esta vista funciona como portada del sistema. Las imagenes se pueden reemplazar luego desde la carpeta publica del proyecto.</p>
    </div>

    <div class="feature-grid">
        <article class="feature-card">
            <div class="feature-icon">01</div>
            <h3>Entrada limpia</h3>
            <p>El inicio queda como portada publica con marca, registro e inicio de sesion.</p>
        </article>

        <article class="feature-card" id="rutas">
            <div class="feature-icon">02</div>
            <h3>Flujo centralizado</h3>
            <p>Los accesos de trabajo aparecen dentro del dashboard despues de iniciar sesion.</p>
        </article>

        <article class="feature-card" id="beneficios">
            <div class="feature-icon">03</div>
            <h3>Visual profesional</h3>
            <p>Diseno oscuro, tarjetas grandes, textos destacados y estructura responsive para escritorio y movil.</p>
        </article>
    </div>
</section>
@endsection
