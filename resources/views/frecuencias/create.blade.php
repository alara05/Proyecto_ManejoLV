@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <p class="text-sm font-medium text-slate-500">Frecuencias autorizadas por la ANT</p>
        <h1 class="text-2xl font-semibold">Registrar frecuencia</h1>
    </div>

    <form method="POST" action="{{ route('frecuencias.store') }}" class="rounded bg-white p-6 shadow-sm">
        @include('frecuencias._form')
    </form>
@endsection
