{{-- resources/views/layouts/app.blade.php --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $appConfig?->nombre_aplicacion ?? config('app.name', 'Manejo Buses') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="admin-shell min-h-screen bg-slate-950 text-slate-100 antialiased"
    style="--app-primary: {{ $appConfig?->color_primario ?? '#24a8ff' }}; --app-secondary: {{ $appConfig?->color_secundario ?? '#ec7519' }};"
>
    <div class="min-h-screen bg-[radial-gradient(circle_at_top_left,rgba(236,117,25,0.16),transparent_32%),radial-gradient(circle_at_top_right,rgba(36,168,255,0.16),transparent_30%),linear-gradient(180deg,#090d15_0%,#05070b_100%)]">
        <header class="sticky top-0 z-40 border-b border-white/10 bg-slate-950/90 shadow-2xl shadow-black/20 backdrop-blur">
            <nav class="flex w-full flex-wrap items-center justify-between gap-4 px-6 py-4 lg:px-10">

                <a
                    href="{{ route('dashboard') }}"
                    class="flex items-center gap-3 text-lg font-black tracking-wide text-white transition hover:opacity-90"
                    title="Ir al dashboard"
                    aria-label="Ir al dashboard"
                >
                    @if ($appConfig?->logo_path)
                        <img
                            src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($appConfig->logo_path) }}"
                            alt="Logo"
                            class="h-11 w-11 rounded-xl bg-white object-cover"
                        >
                    @else
                        <span class="grid h-11 w-11 place-items-center rounded-xl border border-white/30 bg-white text-xs font-black text-slate-950 shadow-lg shadow-sky-500/20">
                            MB
                        </span>
                    @endif

                    <span>{{ $appConfig?->nombre_aplicacion ?? 'Manejo Buses' }}</span>
                </a>

                <div class="flex flex-wrap items-center gap-3 text-sm font-bold">
                    @auth
                        @php
                            $canSellTickets = in_array(auth()->user()?->role, ['admin', 'oficinista'], true);
                            $notificacionesPendientes = auth()->user()->unreadNotifications()->count();

                            $navItems = [
                                [
                                    'label' => 'Buscar viajes',
                                    'route' => route('viajes.buscar'),
                                    'active' => request()->routeIs('viajes.buscar'),
                                ],
                                [
                                    'label' => 'Vender boleto',
                                    'route' => $canSellTickets ? route('boletos.create') : route('cliente.boletos.create'),
                                    'active' => $canSellTickets
                                        ? request()->routeIs('boletos.create', 'boletos.store')
                                        : request()->routeIs('cliente.boletos.create', 'cliente.boletos.store'),
                                ],
                                [
                                    'label' => 'Historial',
                                    'route' => route('cliente.boletos.historial'),
                                    'active' => request()->routeIs('cliente.boletos.historial', 'cliente.boletos.historial.legacy'),
                                ],
                                [
                                    'label' => 'Notificaciones'.($notificacionesPendientes > 0 ? ' ('.$notificacionesPendientes.')' : ''),
                                    'route' => route('notificaciones.index'),
                                    'active' => request()->routeIs('notificaciones.*'),
                                ],
                                [
                                    'label' => 'Cooperativas',
                                    'route' => route('cooperativas.index'),
                                    'active' => request()->routeIs('cooperativas.*'),
                                ],
                                [
                                    'label' => 'Provincias',
                                    'route' => route('provincias.index'),
                                    'active' => request()->routeIs('provincias.*'),
                                ],
                                [
                                    'label' => 'Ciudades',
                                    'route' => route('ciudades.index'),
                                    'active' => request()->routeIs('ciudades.*'),
                                ],
                                [
                                    'label' => 'Buses',
                                    'route' => route('buses.index'),
                                    'active' => request()->routeIs('buses.*'),
                                ],
                                [
                                    'label' => 'Tipos de asientos',
                                    'route' => route('tipo-asientos.index'),
                                    'active' => request()->routeIs('tipo-asientos.*'),
                                ],
                                [
                                    'label' => 'Asientos',
                                    'route' => route('asientos.index'),
                                    'active' => request()->routeIs('asientos.*'),
                                ],
                                [
                                    'label' => 'Rutas',
                                    'route' => route('rutas.index'),
                                    'active' => request()->routeIs('rutas.*'),
                                ],
                                [
                                    'label' => 'Salidas',
                                    'route' => route('salidas.index'),
                                    'active' => request()->routeIs('salidas.*'),
                                ],
                                [
                                    'label' => 'Pagos',
                                    'route' => route('pagos.index'),
                                    'active' => request()->routeIs('pagos.*'),
                                ],
                                [
                                    'label' => 'Accesos',
                                    'route' => route('accesos.index'),
                                    'active' => request()->routeIs('accesos.*'),
                                ],
                                [
                                    'label' => 'Configuración',
                                    'route' => route('configuracion.edit'),
                                    'active' => request()->routeIs('configuracion.*'),
                                ],
                            ];
                        @endphp

                        <label class="sr-only" for="dashboard-navigation">Navegar</label>

                        <select
                            id="dashboard-navigation"
                            class="min-h-12 w-64 rounded-xl border border-white/15 bg-slate-900/95 px-4 font-black text-white shadow-xl shadow-black/25 outline-none transition hover:border-sky-300 focus:border-sky-300 focus:ring-4 focus:ring-sky-400/20"
                            onchange="if (this.value) window.location.href = this.value"
                        >
                            <option value="" selected>Navegar</option>

                            @foreach ($navItems as $item)
                                @unless ($item['active'])
                                    <option value="{{ $item['route'] }}">
                                        {{ $item['label'] }}
                                    </option>
                                @endunless
                            @endforeach
                        </select>

                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                            @csrf

                            <button
                                type="submit"
                                class="rounded-lg border border-sky-400 bg-sky-400 px-4 py-2 font-black text-slate-950 hover:border-sky-300 hover:bg-sky-300"
                            >
                                Salir
                            </button>
                        </form>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="rounded-lg px-3 py-2 text-white/80 hover:bg-white/10 hover:text-white"
                        >
                            Ingresar
                        </a>

                        <a
                            href="{{ route('register') }}"
                            class="rounded-lg border border-sky-400 bg-sky-400 px-4 py-2 font-black text-slate-950 hover:border-sky-300 hover:bg-sky-300"
                        >
                            Registrarse
                        </a>
                    @endauth
                </div>
            </nav>
        </header>

        <main class="w-full px-6 py-8 text-slate-900 lg:px-10">
            @yield('content')
        </main>
    </div>
</body>
</html>
