<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Manejo Buses') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900 antialiased">
    <header class="border-b border-slate-200 bg-white shadow-sm">
        <nav class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-4 px-6 py-4">
            <a href="{{ url('/') }}" class="text-lg font-semibold">Manejo Buses</a>

            <div class="flex flex-wrap items-center gap-3 text-sm">
                @auth
                    <a href="{{ route('dashboard') }}" class="font-medium text-slate-700 hover:text-slate-950">Panel</a>
                    <a href="{{ route('cooperativas.index') }}" class="font-medium text-slate-700 hover:text-slate-950">Cooperativas</a>
                    <a href="{{ route('provincias.index') }}" class="font-medium text-slate-700 hover:text-slate-950">Provincias</a>
                    <a href="{{ route('ciudades.index') }}" class="font-medium text-slate-700 hover:text-slate-950">Ciudades</a>
                    <a href="{{ route('buses.index') }}" class="font-medium text-slate-700 hover:text-slate-950">Buses</a>
                    <a href="{{ route('tipo-asientos.index') }}" class="font-medium text-slate-700 hover:text-slate-950">Tipos de asientos</a>
                    <a href="{{ route('asientos.index') }}" class="font-medium text-slate-700 hover:text-slate-950">Asientos</a>
                    <a href="{{ route('rutas.index') }}" class="font-medium text-slate-700 hover:text-slate-950">Rutas</a>
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="rounded bg-slate-900 px-3 py-2 font-semibold text-white hover:bg-slate-700">
                            Salir
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="font-medium text-slate-700 hover:text-slate-950">Ingresar</a>
                    <a href="{{ route('register') }}" class="rounded bg-slate-900 px-3 py-2 font-semibold text-white hover:bg-slate-700">Registrarse</a>
                @endauth
            </div>
        </nav>
    </header>

    <main class="mx-auto max-w-6xl px-6 py-10">
        @yield('content')
    </main>
</body>
</html>
