@extends('layouts.principal')

@section('title', 'Iniciar sesion | ' . config('app.name', 'Manejo Buses'))

@section('content')
    <section class="auth-page auth-page-login">
        <div class="auth-shell">
            <aside class="auth-visual" style="--auth-image: url('{{ asset('images/inicio/panel-control.jpg') }}')">
                <div class="auth-visual-content">
                    <span class="hero-kicker">Panel seguro</span>
                    <h1>Gestiona buses y ventas</h1>
                    <p>Accede al sistema para administrar cooperativas, rutas, asientos, boletos y pagos desde un solo panel.</p>
                </div>
            </aside>

            <div class="auth-panel">
                <div class="auth-heading">
                    <span>Bienvenido de nuevo</span>
                    <h2>Iniciar sesion</h2>
                    <p>Usa tus credenciales para continuar con la gestion de pasajes.</p>
                </div>

                <form method="POST" action="{{ route('login') }}" class="auth-form">
                    @csrf

                    <div class="form-field">
                        <label for="email">Correo electronico</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus>
                        @error('email')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-field">
                        <label for="password">Contrasena</label>
                        <input id="password" name="password" type="password" required>
                        @error('password')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <label class="auth-check">
                        <input type="checkbox" name="remember">
                        <span>Recordar sesion</span>
                    </label>

                    <button type="submit" class="auth-submit">Ingresar</button>
                </form>

                <p class="auth-switch">
                    Aun no tienes cuenta?
                    <a href="{{ route('register') }}">Crear cuenta</a>
                </p>
            </div>
        </div>
    </section>
@endsection
