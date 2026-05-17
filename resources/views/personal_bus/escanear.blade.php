<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $tituloPanel ?? 'Dashboard' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('assets/web/css/login.css') }}">
</head>
<body>
    <main class="pantalla-login">
        <section class="login-card">
            <div class="encabezado-login">
                <div class="logo-bus">🚌</div>
                <h1>{{ $tituloPanel ?? 'Dashboard' }}</h1>
                <p>{{ $subtituloPanel ?? 'Panel del sistema de pasajes.' }}</p>
            </div>

            <section class="texto-ayuda">
                <p><strong>Usuarios:</strong> {{ $estadisticas['usuarios'] ?? 0 }}</p>
                <p><strong>Cooperativas:</strong> {{ $estadisticas['cooperativas'] ?? 0 }}</p>
                <p><strong>Buses:</strong> {{ $estadisticas['buses'] ?? 0 }}</p>
                <p><strong>Rutas:</strong> {{ $estadisticas['rutas'] ?? 0 }}</p>
                <p><strong>Boletos:</strong> {{ $estadisticas['boletos'] ?? 0 }}</p>
            </section>

            <a href="{{ route('login') }}" class="volver">Cerrar sesión visual</a>
        </section>
    </main>
</body>
</html>
