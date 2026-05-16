<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login Web - Sistema de Pasajes</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('assets/web/css/login.css') }}">
</head>
<body>
    <main class="pantalla-login">
        <section class="login-card">
            <a href="{{ route('inicio') }}" class="volver">← Inicio</a>

            <div class="encabezado-login">
                <div class="logo-bus">🚌</div>
                <h1>Iniciar sesión</h1>
                <p>Accede al sistema de venta de pasajes</p>
            </div>

            <form id="formLoginWeb" class="form-login">
                <label for="email">Correo electrónico</label>
                <input type="email" id="email" name="email" placeholder="correo@ejemplo.com" required>

                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" placeholder="********" required>

                <button type="submit">Ingresar</button>
            </form>

            <div id="mensajeLogin" class="mensaje"></div>

            <p class="texto-ayuda">
                Este login web será usado por administrador, oficinista, cooperativa y usuarios registrados.
            </p>
        </section>
    </main>

    <script src="{{ asset('assets/web/js/login.js') }}"></script>
</body>
</html>
