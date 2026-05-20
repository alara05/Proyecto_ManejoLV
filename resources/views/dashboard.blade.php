@extends('layouts.app')

@section('content')
    @php
        $user = auth()->user();
        $role = $user->role;
    @endphp

    <section class="overflow-hidden rounded-3xl border border-white/10 bg-[linear-gradient(135deg,rgba(236,117,25,0.82),rgba(20,126,220,0.48),rgba(8,12,19,0.72))] p-6 text-white shadow-2xl shadow-black/30 md:p-8">
        <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_320px] lg:items-end">
            <div>
                <p class="inline-flex rounded-full border border-white/25 bg-black/20 px-3 py-2 text-xs font-black uppercase tracking-[0.16em] text-white">Sesion iniciada</p>
                <h1 class="mt-5 max-w-3xl text-4xl font-black uppercase leading-none tracking-wide md:text-6xl">
                    Bienvenido, {{ $user->name }}
                </h1>
                <p class="mt-5 max-w-2xl text-base font-semibold leading-7 text-white/80">
                    Usa la barra superior para navegar por viajes, compras, administracion, ventas, pagos y control.
                </p>
            </div>

            <div class="rounded-2xl border border-white/20 bg-slate-950/55 p-6 shadow-xl shadow-black/25 backdrop-blur">
                <p class="text-xs font-black uppercase tracking-[0.14em] text-white/70">Rol actual</p>
                <strong class="mt-3 block text-3xl font-black capitalize text-white">{{ str_replace('_', ' ', $role) }}</strong>
                <p class="mt-3 text-sm font-semibold leading-6 text-white/70">
                    Los accesos principales estan disponibles en la navbar del dashboard.
                </p>
            </div>
        </div>
    </section>
@endsection
