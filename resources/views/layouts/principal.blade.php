<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Sistema de Pasajes'))</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/interfaz-principal.css') }}">
    @stack('styles')
</head>
<body>
    <header class="main-header">
        <nav class="main-navbar" aria-label="Menu principal">
            <a class="brand" href="{{ url('/') }}" aria-label="Ir al inicio">
                <span class="brand-icon">LV</span>
                <span class="brand-text">{{ config('app.name', 'Manejo LV') }}</span>
            </a>

            <button class="menu-toggle" type="button" aria-label="Abrir menu" aria-expanded="false" data-menu-toggle>
                <span></span>
                <span></span>
                <span></span>
            </button>

            <div class="nav-content" data-nav-content>
                <ul class="nav-links">
                    <li><a href="{{ url('/') }}#inicio">Inicio</a></li>
                    <li><a href="{{ url('/') }}#servicios">Servicios</a></li>
                    <li><a href="{{ url('/') }}#rutas">Rutas</a></li>
                    <li><a href="{{ url('/') }}#beneficios">Beneficios</a></li>
                    <li><a href="{{ url('/') }}#contacto">Ayuda</a></li>
                </ul>

                <div class="nav-actions">
                    <a class="btn btn-secondary" href="{{ url('/register') }}">Registrarse</a>
                    <a class="btn btn-primary" href="{{ url('/login') }}">Iniciar sesion</a>
                </div>
            </div>
        </nav>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="main-footer" id="contacto">
        <p>© {{ date('Y') }} {{ config('app.name', 'Manejo LV') }}. Plataforma de gestion de pasajes.</p>
        <p>Atencion, rutas, boletos y administracion desde un solo sistema.</p>
    </footer>

    <script src="{{ asset('js/interfaz-principal.js') }}" defer></script>
    @stack('scripts')
</body>
</html>
