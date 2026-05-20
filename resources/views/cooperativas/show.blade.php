@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-sm font-medium text-slate-500">Detalle de cooperativa</p>
            <h1 class="text-2xl font-semibold">{{ $cooperativa->nombre }}</h1>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('cooperativas.edit', $cooperativa) }}" class="rounded bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
                Editar
            </a>
            <a href="{{ route('cooperativas.index') }}" class="rounded border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Volver
            </a>
        </div>
    </div>

    <section class="rounded bg-white p-6 shadow-sm">
        <div class="mb-6 flex flex-wrap gap-3">
            @include('partials.status-badge', ['active' => $cooperativa->activa, 'trueLabel' => 'Activa', 'falseLabel' => 'Inactiva'])
            <span class="rounded bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">{{ $cooperativa->buses_count }} buses</span>
            <span class="rounded bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">{{ $cooperativa->rutas_count }} rutas</span>
            <span class="rounded bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">{{ $cooperativa->usuarios_count }} usuarios</span>
        </div>

        <div class="grid gap-4 text-sm md:grid-cols-2">
            <div>
                <p class="font-semibold text-slate-500">RUC</p>
                <p class="mt-1 text-slate-900">{{ $cooperativa->ruc ?? 'Sin registrar' }}</p>
            </div>
            <div>
                <p class="font-semibold text-slate-500">Telefono</p>
                <p class="mt-1 text-slate-900">{{ $cooperativa->telefono ?? 'Sin registrar' }}</p>
            </div>
            <div>
                <p class="font-semibold text-slate-500">Correo</p>
                <p class="mt-1 text-slate-900">{{ $cooperativa->email ?? 'Sin registrar' }}</p>
            </div>
            <div>
                <p class="font-semibold text-slate-500">Logo</p>
                <p class="mt-1 text-slate-900">{{ $cooperativa->logo_path ?? 'Sin registrar' }}</p>
            </div>
            <div class="md:col-span-2">
                <p class="font-semibold text-slate-500">Direccion</p>
                <p class="mt-1 text-slate-900">{{ $cooperativa->direccion ?? 'Sin registrar' }}</p>
            </div>
        </div>
    </section>
@endsection
