@extends('layouts.app')

@section('content')
    <section class="mx-auto max-w-md">
        <h1 class="text-2xl font-semibold">Crear cuenta</h1>

        <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-5 rounded bg-white p-6 shadow-sm">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium">Nombre completo</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus
                    class="mt-2 w-full rounded border border-slate-300 px-3 py-2 focus:border-slate-900 focus:outline-none">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="cedula" class="block text-sm font-medium">Cedula</label>
                <input id="cedula" name="cedula" type="text" value="{{ old('cedula') }}" maxlength="10"
                    class="mt-2 w-full rounded border border-slate-300 px-3 py-2 focus:border-slate-900 focus:outline-none">
                @error('cedula')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="telefono" class="block text-sm font-medium">Telefono</label>
                <input id="telefono" name="telefono" type="text" value="{{ old('telefono') }}" maxlength="20"
                    class="mt-2 w-full rounded border border-slate-300 px-3 py-2 focus:border-slate-900 focus:outline-none">
                @error('telefono')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium">Correo electronico</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required
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

            <div>
                <label for="password_confirmation" class="block text-sm font-medium">Confirmar contrasena</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required
                    class="mt-2 w-full rounded border border-slate-300 px-3 py-2 focus:border-slate-900 focus:outline-none">
            </div>

            <button type="submit" class="w-full rounded bg-slate-900 px-4 py-2 font-medium text-white hover:bg-slate-700">
                Registrarme
            </button>
        </form>
    </section>
@endsection
