@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <p class="text-sm font-medium text-slate-500">Nueva provincia</p>
        <h1 class="text-2xl font-semibold">Registrar provincia</h1>
    </div>

    <section class="rounded bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('provincias.store') }}">
            @include('provincias._form')
        </form>
    </section>
@endsection
