@extends('layouts.app')

@section('content')
    <section class="mx-auto max-w-md">
        <h1 class="text-2xl font-semibold">Iniciar sesion</h1>

        <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-5 rounded bg-white p-6 shadow-sm">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium">Correo electronico</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                    class="mt-2 w-full rounded border border-slate-300 px-3 py-2 focus:border-slate-900 focus:outline-none">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium">Contrasena</label>
                <input id="password" name="password" type="password" required
                    class="mt-2 w-full rounded border border-slate-300 px-3 py-2 focus:border-slate-900 focus:outline-none">
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="remember" class="rounded border-slate-300">
                Recordar sesion
            </label>

            <button type="submit" class="w-full rounded bg-slate-900 px-4 py-2 font-medium text-white hover:bg-slate-700">
                Ingresar
            </button>
        </form>
    </section>
@endsection
