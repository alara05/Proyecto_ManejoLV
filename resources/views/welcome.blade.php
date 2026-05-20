@extends('layouts.principal')

@section('title', 'Inicio | Cuchao')

@section('content')
    <style>
        .main-header,
        .main-footer {
            display: none;
        }

        main {
            min-height: 100vh;
        }

        .home-showcase {
            min-height: 100vh;
            padding: 18px;
            background:
                radial-gradient(circle at 18% 8%, rgba(37, 99, 235, 0.18), transparent 30%),
                radial-gradient(circle at 78% 12%, rgba(249, 115, 22, 0.12), transparent 34%),
                linear-gradient(180deg, #020713 0%, #030711 52%, #02050b 100%);
            color: #fff;
            font-family: Figtree, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .home-frame {
            max-width: 1780px;
            margin: 0 auto;
        }

        .home-hero,
        .home-band,
        .home-tile,
        .home-strip {
            border: 1px solid rgba(148, 163, 184, 0.18);
            box-shadow: 0 22px 70px rgba(0, 0, 0, 0.28);
        }

        .home-hero {
            position: relative;
            min-height: 560px;
            overflow: hidden;
            border-radius: 10px;
            background: #07111f;
        }

        .home-hero::before {
            position: absolute;
            inset: 0;
            z-index: 1;
            content: "";
            background:
                linear-gradient(90deg, rgba(2, 6, 16, 0.96) 0%, rgba(2, 6, 16, 0.78) 33%, rgba(2, 6, 16, 0.2) 62%, rgba(2, 6, 16, 0.66) 100%),
                linear-gradient(180deg, rgba(2, 6, 16, 0.15), rgba(2, 6, 16, 0.82));
        }

        .home-hero-image {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: 64% center;
        }

        .home-hero-content {
            position: relative;
            z-index: 2;
            display: grid;
            min-height: 560px;
            grid-template-columns: minmax(590px, 0.9fr) minmax(320px, 1fr);
            gap: 24px;
            align-items: end;
            padding: 60px 74px;
        }

        .home-eyebrow {
            margin: 0 0 26px;
            color: #ff7a00;
            font-size: 15px;
            font-weight: 900;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        .home-logo {
            display: block;
            width: min(420px, 82vw);
            height: auto;
            margin: 0 0 22px;
            filter: drop-shadow(0 18px 34px rgba(0, 0, 0, 0.42));
        }

        .home-title {
            max-width: 600px;
            margin: 0;
            color: #fff;
            font-size: clamp(52px, 6vw, 90px);
            font-weight: 900;
            line-height: 0.96;
            letter-spacing: 0;
            text-shadow: 0 8px 30px rgba(0, 0, 0, 0.42);
        }

        .home-title span {
            display: block;
            color: #ff7300;
        }

        .home-copy {
            max-width: 560px;
            margin: 24px 0 0;
            color: rgba(255, 255, 255, 0.88);
            font-size: 21px;
            font-weight: 500;
            line-height: 1.55;
        }

        .home-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 36px;
        }

        .home-button {
            display: inline-flex;
            min-width: 218px;
            min-height: 64px;
            align-items: center;
            justify-content: center;
            gap: 13px;
            border-radius: 999px;
            font-size: 18px;
            font-weight: 900;
            text-decoration: none;
            transition: transform 180ms ease, border-color 180ms ease, background 180ms ease;
        }

        .home-button:hover {
            transform: translateY(-2px);
        }

        .home-button-primary {
            background: linear-gradient(135deg, #ff8a00, #ff6200);
            color: #fff;
            box-shadow: 0 16px 34px rgba(255, 112, 0, 0.24);
        }

        .home-button-secondary {
            border: 1px solid rgba(226, 232, 240, 0.34);
            background: rgba(2, 6, 16, 0.34);
            color: #fff;
            backdrop-filter: blur(12px);
        }

        .home-note {
            align-self: end;
            justify-self: end;
            width: min(310px, 100%);
            margin-bottom: 12px;
            padding: 28px;
            border: 1px solid rgba(255, 255, 255, 0.22);
            border-radius: 28px;
            background: rgba(15, 23, 42, 0.76);
            box-shadow: 0 22px 60px rgba(0, 0, 0, 0.38);
            backdrop-filter: blur(14px);
        }

        .home-note-icon,
        .home-feature-icon,
        .home-strip-icon {
            display: grid;
            place-items: center;
            flex: 0 0 auto;
        }

        .home-note-icon {
            width: 56px;
            height: 56px;
            margin-bottom: 18px;
            border-radius: 14px;
            background: rgba(255, 113, 0, 0.18);
            color: #ff7a00;
        }

        .home-note strong {
            display: block;
            color: #fff;
            font-size: 22px;
            font-weight: 900;
            line-height: 1.24;
        }

        .home-note p {
            margin: 12px 0 0;
            color: #ff7a00;
            font-size: 15px;
            font-weight: 800;
        }

        .home-band {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0;
            margin-top: 20px;
            overflow: hidden;
            border-radius: 10px;
            background: rgba(4, 12, 25, 0.86);
        }

        .home-feature {
            display: flex;
            gap: 26px;
            align-items: center;
            min-height: 142px;
            padding: 24px 72px;
        }

        .home-feature + .home-feature {
            border-left: 1px solid rgba(148, 163, 184, 0.2);
        }

        .home-feature-icon,
        .home-strip-icon {
            width: 78px;
            height: 78px;
            border-radius: 22px;
            background: linear-gradient(145deg, rgba(30, 41, 59, 0.92), rgba(15, 23, 42, 0.88));
            color: var(--feature-color, #ff7300);
        }

        .home-feature h2,
        .home-strip h3 {
            margin: 0;
            color: #fff;
            font-size: 21px;
            font-weight: 900;
            line-height: 1.25;
        }

        .home-feature p,
        .home-strip p {
            margin: 8px 0 0;
            color: rgba(226, 232, 240, 0.84);
            font-size: 17px;
            line-height: 1.48;
        }

        .home-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 22px;
            margin-top: 22px;
        }

        .home-tile {
            position: relative;
            min-height: 315px;
            overflow: hidden;
            border-radius: 10px;
            background: #0b1324;
        }

        .home-tile::before {
            position: absolute;
            inset: 0;
            z-index: 1;
            content: "";
            background: linear-gradient(90deg, rgba(2, 6, 16, 0.82), rgba(2, 6, 16, 0.22) 58%, rgba(2, 6, 16, 0.1));
        }

        .home-tile img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .home-tile-content {
            position: relative;
            z-index: 2;
            width: min(62%, 330px);
            padding: 44px 40px;
        }

        .home-tile h2 {
            margin: 0;
            color: #fff;
            font-size: 34px;
            font-weight: 900;
            line-height: 1.08;
        }

        .home-tile p {
            margin: 18px 0 0;
            color: rgba(255, 255, 255, 0.9);
            font-size: 17px;
            line-height: 1.52;
        }

        .home-strip {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
            margin-top: 20px;
            padding: 22px 52px;
            border-radius: 10px;
            background: rgba(4, 12, 25, 0.9);
        }

        .home-strip-item {
            display: flex;
            align-items: center;
            gap: 20px;
            min-width: 0;
        }

        .home-strip-icon {
            width: 62px;
            height: 62px;
            border-radius: 20px;
        }

        .home-strip h3 {
            font-size: 18px;
        }

        .home-strip p {
            font-size: 16px;
        }

        .home-icon {
            width: 30px;
            height: 30px;
            stroke: currentColor;
            stroke-width: 2.2;
            stroke-linecap: round;
            stroke-linejoin: round;
            fill: none;
        }

        @media (max-width: 1200px) {
            .home-hero-content {
                grid-template-columns: 1fr;
                padding: 46px;
            }

            .home-note {
                justify-self: start;
            }

            .home-feature {
                padding: 24px 34px;
            }

            .home-grid,
            .home-strip {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 820px) {
            .home-showcase {
                padding: 12px;
            }

            .home-hero,
            .home-hero-content {
                min-height: auto;
            }

            .home-hero-content {
                padding: 34px 24px;
            }

            .home-title {
                font-size: clamp(46px, 14vw, 68px);
            }

            .home-logo {
                width: min(320px, 88vw);
            }

            .home-copy {
                font-size: 18px;
            }

            .home-button {
                width: 100%;
            }

            .home-band,
            .home-grid,
            .home-strip {
                grid-template-columns: 1fr;
            }

            .home-feature + .home-feature {
                border-top: 1px solid rgba(148, 163, 184, 0.2);
                border-left: 0;
            }

            .home-feature,
            .home-strip {
                padding: 22px;
            }

            .home-tile {
                min-height: 270px;
            }

            .home-tile-content {
                width: min(78%, 340px);
                padding: 32px 24px;
            }
        }
    </style>

    <div class="home-showcase">
        <div class="home-frame">
            <section class="home-hero" aria-labelledby="home-title">
                <img class="home-hero-image" src="{{ asset('images/dashboard/bus.png') }}" alt="Bus Cuchao">

                <div class="home-hero-content">
                    <div>
                        <img class="home-logo" src="{{ asset('images/dashboard/logo.png') }}" alt="Cuchao Gestor de Buses">
                        <p class="home-eyebrow">Sistema de gestión de transporte</p>
                        <h1 class="home-title" id="home-title">
                            Bienvenido a
                            <span>Cuchao</span>
                        </h1>
                        <p class="home-copy">
                            La plataforma integral que simplifica la gestión de buses, rutas, boletos y pasajeros. Todo lo que necesitas, en un solo lugar.
                        </p>

                        <div class="home-actions">
                            <a class="home-button home-button-primary" href="{{ route('login') }}">
                                <svg class="home-icon" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                    <circle cx="12" cy="7" r="4" />
                                </svg>
                                Iniciar sesión
                            </a>

                            <a class="home-button home-button-secondary" href="{{ route('register') }}">
                                <svg class="home-icon" viewBox="0 0 24 24" aria-hidden="true">
                                    <rect x="4" y="5" width="16" height="14" rx="3" />
                                    <path d="M12 9v6" />
                                    <path d="M9 12h6" />
                                </svg>
                                Registrarse
                            </a>
                        </div>
                    </div>

                    <aside class="home-note" aria-label="Mensaje destacado">
                        <span class="home-note-icon">
                            <svg class="home-icon" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M8 6v6" />
                                <path d="M16 6v6" />
                                <path d="M3 11h18" />
                                <path d="M5 18h2" />
                                <path d="M17 18h2" />
                                <rect x="5" y="4" width="14" height="13" rx="2" />
                            </svg>
                        </span>
                        <strong>Viajes seguros, puntuales y confiables</strong>
                        <p>Tu viaje, nuestra prioridad.</p>
                    </aside>
                </div>
            </section>

            <section class="home-band" aria-label="Beneficios principales">
                <article class="home-feature" style="--feature-color: #ff7300">
                    <span class="home-feature-icon">
                        <svg class="home-icon" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M20.6 13.4 12 22l-9-9V4h9l8.6 8.6a1.2 1.2 0 0 1 0 1.7Z" />
                            <circle cx="8" cy="8" r="1.4" />
                        </svg>
                    </span>
                    <div>
                        <h2>Fácil de usar</h2>
                        <p>Interfaz intuitiva y moderna para todos los usuarios.</p>
                    </div>
                </article>

                <article class="home-feature" style="--feature-color: #3b82f6">
                    <span class="home-feature-icon">
                        <svg class="home-icon" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M4 19V9" />
                            <path d="M10 19V5" />
                            <path d="M16 19v-8" />
                            <path d="M22 19V3" />
                            <path d="M2 19h22" />
                        </svg>
                    </span>
                    <div>
                        <h2>Información en tiempo real</h2>
                        <p>Datos actualizados al instante para mejores decisiones.</p>
                    </div>
                </article>

                <article class="home-feature" style="--feature-color: #20d582">
                    <span class="home-feature-icon">
                        <svg class="home-icon" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z" />
                            <path d="m9 12 2 2 4-5" />
                        </svg>
                    </span>
                    <div>
                        <h2>Seguro y confiable</h2>
                        <p>Protegemos tu información y la de tus pasajeros.</p>
                    </div>
                </article>
            </section>

            <section class="home-grid" aria-label="Caracteristicas del sistema">
                <article class="home-tile">
                    <img src="{{ asset('images/dashboard/celular.png') }}" alt="Boleto móvil">
                    <div class="home-tile-content">
                        <h2>Gestiona tus boletos</h2>
                        <p>Consulta rutas, salidas y disponibilidad desde una interfaz rápida y moderna.</p>
                    </div>
                </article>

                <article class="home-tile">
                    <img src="{{ asset('images/dashboard/ruta.png') }}" alt="Ruta de viaje">
                    <div class="home-tile-content">
                        <h2>Encuentra tu viaje</h2>
                        <p>Organiza la información principal de ciudades, frecuencias, buses y cooperativas.</p>
                    </div>
                </article>

                <article class="home-tile">
                    <img src="{{ asset('images/dashboard/laptop.png') }}" alt="Panel administrativo">
                    <div class="home-tile-content">
                        <h2>Administra el sistema</h2>
                        <p>Accede con tus credenciales para controlar usuarios, ventas, buses, asientos y reportes.</p>
                    </div>
                </article>
            </section>

            <section class="home-strip" aria-label="Resumen de ventajas">
                <article class="home-strip-item" style="--feature-color: #ff7300">
                    <span class="home-strip-icon">
                        <svg class="home-icon" viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="12" cy="12" r="9" />
                            <path d="M12 7v5l3 2" />
                        </svg>
                    </span>
                    <div>
                        <h3>Ahorra tiempo</h3>
                        <p>Procesos más rápidos y eficientes</p>
                    </div>
                </article>

                <article class="home-strip-item" style="--feature-color: #3b82f6">
                    <span class="home-strip-icon">
                        <svg class="home-icon" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M16 21v-2a4 4 0 0 0-8 0v2" />
                            <circle cx="12" cy="7" r="4" />
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                        </svg>
                    </span>
                    <div>
                        <h3>Mejor organización</h3>
                        <p>Información centralizada</p>
                    </div>
                </article>

                <article class="home-strip-item" style="--feature-color: #20d582">
                    <span class="home-strip-icon">
                        <svg class="home-icon" viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="12" cy="12" r="9" />
                            <path d="m8.5 12.5 2.5 2.5 5-6" />
                        </svg>
                    </span>
                    <div>
                        <h3>Mayor control</h3>
                        <p>Reportes y estadísticas precisas</p>
                    </div>
                </article>

                <article class="home-strip-item" style="--feature-color: #8b5cf6">
                    <span class="home-strip-icon">
                        <svg class="home-icon" viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="12" cy="12" r="9" />
                            <path d="M8 14s1.5 2 4 2 4-2 4-2" />
                            <path d="M9 9h.01" />
                            <path d="M15 9h.01" />
                        </svg>
                    </span>
                    <div>
                        <h3>Mejor experiencia</h3>
                        <p>Para ti y tus pasajeros</p>
                    </div>
                </article>
            </section>
        </div>
    </div>
@endsection
