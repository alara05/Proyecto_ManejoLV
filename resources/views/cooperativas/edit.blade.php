@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <p class="text-sm font-medium text-slate-500">Administracion</p>
        <h1 class="text-2xl font-semibold">Editar cooperativa</h1>
        <p class="mt-1 text-sm text-slate-500">{{ $cooperativa->nombre }}</p>
    </div>

    <form method="POST" action="{{ route('cooperativas.update', $cooperativa) }}" class="rounded bg-white p-6 shadow-sm">
        @method('PUT')
        @include('cooperativas._form')
    </form>
@endsection
