<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Pasajes</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('assets/web/css/login.css') }}">
</head>
<body>
    <main class="landing">
        <section class="landing-card">
            <div class="marca">
                <div class="logo-bus">🚌</div>
                <div>
                    <h1>Sistema de Venta de Pasajes</h1>
                    <p>Gestión y compra de boletos interprovinciales</p>
                </div>
            </div>

            <div class="acciones-landing">
                <a class="btn-principal" href="{{ route('login') }}">Ingresar al sistema web</a>
                <p class="texto-ayuda">
                    La app mobile está en la carpeta <strong>Mobile</strong> y es solo para usuarios que compran boletos.
                </p>
            </div>

            <section class="modulos">
                <article>
                    <h3>Usuarios</h3>
                    <p>Buscar viajes, comprar boletos y consultar historial.</p>
                </article>
                <article>
                    <h3>Oficinista</h3>
                    <p>Venta de boletos y validación de comprobantes.</p>
                </article>
                <article>
                    <h3>Cooperativa</h3>
                    <p>Gestión de buses, frecuencias y hojas de ruta.</p>
                </article>
            </section>
        </section>
    </main>
</body>
</html>
