@extends('layouts.app')

@section('content')
    <section class="rounded bg-white p-6 shadow-sm">
        <p class="text-sm font-medium text-slate-500">Sesion iniciada</p>
        <h1 class="mt-2 text-2xl font-semibold">Bienvenido, {{ auth()->user()->name }}</h1>
        <p class="mt-2 text-slate-600">Rol actual: {{ auth()->user()->role }}</p>
    </section>
@endsection
