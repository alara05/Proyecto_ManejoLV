@extends('layouts.principal')

@section('title', 'Crear cuenta | Cuchao')

@section('content')
    <section class="auth-page auth-page-register">
        <div class="auth-shell">
            <aside class="auth-visual" style="--auth-image: url('{{ asset('images/inicio/panel-boletos.jpg') }}')">
                <div class="auth-visual-content">
                    <span class="hero-kicker">Sistema de pasajes</span>
                    <h1>Viaja en bus por Ecuador</h1>
                    <p>Crea tu cuenta para consultar rutas, revisar salidas y preparar la compra de boletos en la plataforma.</p>
                </div>
            </aside>

            <div class="auth-panel">
                <div class="auth-heading">
                    <span>Nuevo usuario</span>
                    <h2>Crear cuenta</h2>
                    <p>Registra tus datos para empezar a usar el sistema de pasajes.</p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="auth-form auth-form-grid">
                    @csrf

                    <div class="form-field form-field-wide">
                        <label for="name">Nombre Usuario</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus>
                        @error('name')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-field">
                        <label for="cedula">Cedula</label>
                        <input id="cedula" name="cedula" type="text" value="{{ old('cedula') }}" maxlength="10">
                        @error('cedula')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-field">
                        <label for="telefono">Telefono</label>
                        <input id="telefono" name="telefono" type="text" value="{{ old('telefono') }}" maxlength="20">
                        @error('telefono')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-field form-field-wide">
                        <label for="email">Correo electronico</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required>
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

                    <div class="form-field">
                        <label for="password_confirmation">Confirmar contrasena</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required>
                    </div>

                    <button type="submit" class="auth-submit form-field-wide">Registrarme</button>
                </form>

                <p class="auth-switch">
                    Ya tienes cuenta?
                    <a href="{{ route('login') }}">Iniciar sesion</a>
                </p>
            </div>
        </div>
    </section>
@endsection
