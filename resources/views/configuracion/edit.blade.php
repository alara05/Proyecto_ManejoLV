@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <p class="text-sm font-medium text-slate-500">Administracion</p>
        <h1 class="text-2xl font-semibold">Configuracion de la aplicacion</h1>
    </div>

    @include('partials.flash')
    @include('partials.validation-errors')

    <form method="POST" action="{{ route('configuracion.update') }}" enctype="multipart/form-data" class="rounded bg-white p-6 shadow-sm">
        @csrf
        @method('PUT')

        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label for="nombre_aplicacion" class="block text-sm font-semibold text-slate-700">Nombre de la aplicacion</label>
                <input id="nombre_aplicacion" name="nombre_aplicacion" type="text"
                    value="{{ old('nombre_aplicacion', $configuracion->nombre_aplicacion) }}" required
                    class="mt-2 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
            </div>

            <div>
                <label for="logo" class="block text-sm font-semibold text-slate-700">Logo</label>
                <input id="logo" name="logo" type="file" accept=".jpg,.jpeg,.png,.webp,.svg"
                    class="mt-2 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
                @if ($configuracion->logo_path)
                    <p class="mt-2 text-xs text-slate-500">Logo actual: {{ $configuracion->logo_path }}</p>
                @endif
            </div>

            <div>
                <label for="color_primario" class="block text-sm font-semibold text-slate-700">Color primario</label>
                <input id="color_primario" name="color_primario" type="color"
                    value="{{ old('color_primario', $configuracion->color_primario) }}" required
                    class="mt-2 h-11 w-full rounded border border-slate-300 px-2 py-1 focus:border-slate-900 focus:outline-none">
            </div>

            <div>
                <label for="color_secundario" class="block text-sm font-semibold text-slate-700">Color secundario</label>
                <input id="color_secundario" name="color_secundario" type="color"
                    value="{{ old('color_secundario', $configuracion->color_secundario) }}" required
                    class="mt-2 h-11 w-full rounded border border-slate-300 px-2 py-1 focus:border-slate-900 focus:outline-none">
            </div>

            <div>
                <label for="email_soporte" class="block text-sm font-semibold text-slate-700">Email de soporte</label>
                <input id="email_soporte" name="email_soporte" type="email"
                    value="{{ old('email_soporte', $configuracion->email_soporte) }}"
                    class="mt-2 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
            </div>

            <div>
                <label for="telefono_soporte" class="block text-sm font-semibold text-slate-700">Telefono de soporte</label>
                <input id="telefono_soporte" name="telefono_soporte" type="text" maxlength="20"
                    value="{{ old('telefono_soporte', $configuracion->telefono_soporte) }}"
                    class="mt-2 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
            </div>
        </div>

        <section class="mt-8 rounded border border-slate-200 bg-slate-50 p-4">
            <h2 class="text-base font-semibold text-slate-900">Redes sociales</h2>
            <div class="mt-4 grid gap-5 md:grid-cols-2">
                @foreach (['facebook' => 'Facebook', 'instagram' => 'Instagram', 'x' => 'X / Twitter', 'whatsapp' => 'WhatsApp'] as $key => $label)
                    <div>
                        <label for="red_{{ $key }}" class="block text-sm font-semibold text-slate-700">{{ $label }}</label>
                        <input id="red_{{ $key }}" name="redes_sociales[{{ $key }}]" type="url"
                            value="{{ old('redes_sociales.' . $key, data_get($configuracion->redes_sociales, $key)) }}"
                            placeholder="https://..."
                            class="mt-2 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
                    </div>
                @endforeach
            </div>
        </section>

        <div class="mt-6 flex flex-wrap gap-3">
            <button type="submit" class="rounded bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
                Guardar configuracion
            </button>
            <a href="{{ route('dashboard') }}" class="rounded border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Cancelar
            </a>
        </div>
    </form>
@endsection
