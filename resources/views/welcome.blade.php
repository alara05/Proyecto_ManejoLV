@extends('layouts.app')

@section('content')
    <section class="grid gap-8 py-10 md:grid-cols-[1.2fr_0.8fr] md:items-center">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wide text-amber-600">Transporte interprovincial</p>
            <h1 class="mt-3 max-w-2xl text-4xl font-bold text-slate-950">
                Gestion y venta de pasajes para cooperativas del Ecuador
            </h1>
            <p class="mt-4 max-w-xl text-lg text-slate-600">
                Plataforma base para administrar cooperativas, frecuencias, buses, boletos y acceso de pasajeros.
            </p>

            <div class="mt-8 flex flex-wrap gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="rounded bg-slate-900 px-5 py-3 font-medium text-white hover:bg-slate-700">
                        Ir al panel
                    </a>
                @else
                    <a href="{{ route('login') }}" class="rounded bg-slate-900 px-5 py-3 font-medium text-white hover:bg-slate-700">
                        Ingresar
                    </a>
                    <a href="{{ route('register') }}" class="rounded border border-slate-300 px-5 py-3 font-medium text-slate-800 hover:bg-white">
                        Crear cuenta
                    </a>
                @endauth
            </div>
        </div>

        <div class="rounded bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold">Autenticacion activa</h2>
            <ul class="mt-4 space-y-3 text-sm text-slate-600">
                <li>Registro de clientes con nombre, cedula, telefono, email y contrasena.</li>
                <li>Inicio de sesion con validacion de usuario activo.</li>
                <li>Cierre de sesion seguro con invalidacion de sesion.</li>
                <li>Panel protegido para usuarios autenticados.</li>
            </ul>
        </div>
    </section>
@endsection
