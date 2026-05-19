@extends('layouts.app')

@section('content')
    <section class="rounded bg-white p-6 shadow-sm">
        <p class="text-sm font-medium text-slate-500">Sesion iniciada</p>
        <h1 class="mt-2 text-2xl font-semibold">Bienvenido, {{ auth()->user()->name }}</h1>
        <p class="mt-2 text-slate-600">Rol actual: {{ auth()->user()->role }}</p>
        <div class="mt-5 flex flex-wrap gap-3">
            <a href="{{ route('cooperativas.index') }}" class="inline-flex rounded bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
                Gestionar cooperativas
            </a>
            <a href="{{ route('provincias.index') }}" class="inline-flex rounded border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Gestionar provincias
            </a>
            <a href="{{ route('ciudades.index') }}" class="inline-flex rounded border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Gestionar ciudades
            </a>
            <a href="{{ route('buses.index') }}" class="inline-flex rounded border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Gestionar buses
            </a>
            <a href="{{ route('tipo-asientos.index') }}" class="inline-flex rounded border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Gestionar tipos de asientos
            </a>
            <a href="{{ route('asientos.index') }}" class="inline-flex rounded border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Gestionar asientos
            </a>
            <a href="{{ route('rutas.index') }}" class="inline-flex rounded border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Gestionar rutas
            </a>
            <a href="{{ route('salidas.index') }}" class="inline-flex rounded border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Gestionar salidas
            </a>
            @if (in_array(auth()->user()->role, ['admin', 'oficinista'], true))
                <a href="{{ route('boletos.index') }}" class="inline-flex rounded border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Vender boletos
                </a>
                <a href="{{ route('pagos.index') }}" class="inline-flex rounded border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Validar pagos
                </a>
            @endif
            @if (in_array(auth()->user()->role, ['admin', 'personal_bus'], true))
                <a href="{{ route('accesos.index') }}" class="inline-flex rounded border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Registrar accesos
                </a>
            @endif
        </div>
    </section>
@endsection
