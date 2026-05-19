<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $appConfig?->nombre_aplicacion ?? config('app.name', 'Manejo Buses') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900 antialiased" style="--app-primary: {{ $appConfig?->color_primario ?? '#0f172a' }}; --app-secondary: {{ $appConfig?->color_secundario ?? '#f59e0b' }};">
    <header class="border-b border-slate-200 bg-white shadow-sm">
        <nav class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-4 px-6 py-4">
            <a href="{{ url('/') }}" class="flex items-center gap-2 text-lg font-semibold">
                @if ($appConfig?->logo_path)
                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($appConfig->logo_path) }}" alt="Logo" class="h-9 w-9 rounded object-cover">
                @endif
                <span>{{ $appConfig?->nombre_aplicacion ?? 'Manejo Buses' }}</span>
            </a>

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
                    <a href="{{ route('salidas.index') }}" class="font-medium text-slate-700 hover:text-slate-950">Salidas</a>
                    @if (in_array(auth()->user()->role, ['admin', 'oficinista'], true))
                        <a href="{{ route('boletos.index') }}" class="font-medium text-slate-700 hover:text-slate-950">Boletos</a>
                        <a href="{{ route('pagos.index') }}" class="font-medium text-slate-700 hover:text-slate-950">Pagos</a>
                    @endif
                    @if (in_array(auth()->user()->role, ['admin', 'personal_bus'], true))
                        <a href="{{ route('accesos.index') }}" class="font-medium text-slate-700 hover:text-slate-950">Accesos</a>
                    @endif
                    @if (auth()->user()->role === 'admin')
                        <a href="{{ route('configuracion.edit') }}" class="font-medium text-slate-700 hover:text-slate-950">Configuracion</a>
                    @endif
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
