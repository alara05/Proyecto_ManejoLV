@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <p class="text-sm font-medium text-slate-500">Gestion de flota</p>
        <h1 class="text-2xl font-semibold">Editar bus {{ $bus->numero }}</h1>
    </div>

    <form method="POST" action="{{ route('buses.update', $bus) }}" class="rounded bg-white p-6 shadow-sm">
        @method('PUT')
        @include('buses._form')
    </form>
@endsection
