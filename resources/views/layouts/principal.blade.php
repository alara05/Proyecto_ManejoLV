{{-- resources/views/layouts/principal.blade.php --}}

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Cuchao')</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/interfaz-principal.css') }}">

    @stack('styles')
</head>

<body style="--accent: {{ $appConfig?->color_primario ?? '#24a8ff' }}; --accent-hover: {{ $appConfig?->color_secundario ?? '#4ab8ff' }};">
    <header class="main-header">
        <nav class="main-navbar" aria-label="Menu principal">

            <a
                class="brand"
                href="{{ auth()->check() ? route('dashboard') : route('inicio') }}"
                aria-label="{{ auth()->check() ? 'Ir al dashboard' : 'Ir al inicio' }}"
                title="{{ auth()->check() ? 'Ir al dashboard' : 'Ir al inicio' }}"
            >
                <img
                    src="{{ asset('images/dashboard/logo-header.png') }}"
                    alt="Cuchao"
                    class="brand-logo"
                >

            </a>

            <div class="nav-actions nav-actions-public">
                @auth
                    @php
                        $canSellTickets = in_array(auth()->user()?->role, ['admin', 'oficinista'], true);

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
                        ];
                    @endphp

                    <label class="sr-only" for="public-dashboard-navigation">Navegar</label>

                    <select
                        id="public-dashboard-navigation"
                        class="nav-select"
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

                    <form method="POST" action="{{ route('logout') }}" class="logout-form">
                        @csrf

                        <button class="btn btn-primary" type="submit">
                            Salir
                        </button>
                    </form>
                @else
                    <a class="btn btn-secondary" href="{{ route('register') }}">
                        Registrarse
                    </a>

                    <a class="btn btn-primary" href="{{ route('login') }}">
                        Iniciar sesion
                    </a>
                @endauth
            </div>
        </nav>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="main-footer" id="contacto">
        <p>
            &copy; {{ date('Y') }}
            Cuchao.
            Plataforma de gestion de pasajes.
        </p>

        <p>
            {{ $appConfig?->email_soporte ?? 'Atencion, rutas, boletos y administracion desde un solo sistema.' }}

            @if ($appConfig?->telefono_soporte)
                · {{ $appConfig->telefono_soporte }}
            @endif
        </p>
    </footer>

    <script src="{{ asset('js/interfaz-principal.js') }}" defer></script>

    @stack('scripts')
</body>
</html>
