@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <p class="text-sm font-medium text-slate-500">Generacion manual</p>
        <h1 class="text-2xl font-semibold">Nueva salida / hoja de ruta</h1>
    </div>

    <form method="POST" action="{{ route('salidas.store') }}" class="rounded bg-white p-6 shadow-sm">
        @csrf

        @include('partials.validation-errors')

        <div class="grid gap-5 md:grid-cols-2">
            <div class="md:col-span-2">
                <label for="frecuencia_id" class="block text-sm font-semibold text-slate-700">Frecuencia habilitada</label>
                <select id="frecuencia_id" name="frecuencia_id" required
                    class="mt-2 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
                    <option value="">Seleccione una frecuencia</option>
                    @foreach ($frecuencias as $frecuencia)
                        <option value="{{ $frecuencia->id }}" @selected(old('frecuencia_id') == $frecuencia->id)>
                            {{ \Illuminate\Support\Str::of($frecuencia->hora_salida)->substr(0, 5) }}
                            / {{ $frecuencia->origen->nombre }} - {{ $frecuencia->destino->nombre }}
                            / {{ $frecuencia->cooperativa->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('frecuencia_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="bus_id" class="block text-sm font-semibold text-slate-700">Bus</label>
                <select id="bus_id" name="bus_id" required
                    class="mt-2 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
                    <option value="">Seleccione un bus</option>
                    @foreach ($buses as $bus)
                        <option value="{{ $bus->id }}" @selected(old('bus_id') == $bus->id)>
                            Bus {{ $bus->numero }} - {{ $bus->placa }} / {{ $bus->cooperativa->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('bus_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="fecha" class="block text-sm font-semibold text-slate-700">Fecha de salida</label>
                <input id="fecha" name="fecha" type="date" value="{{ old('fecha', now()->toDateString()) }}" required
                    class="mt-2 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
                @error('fecha')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="precio_base" class="block text-sm font-semibold text-slate-700">Precio base</label>
                <input id="precio_base" name="precio_base" type="number" min="0" step="0.01" value="{{ old('precio_base') }}" required
                    class="mt-2 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
                @error('precio_base')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-6 flex flex-wrap gap-3">
            <button type="submit" class="rounded bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
                Generar salida
            </button>
            <a href="{{ route('salidas.index') }}" class="rounded border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Cancelar
            </a>
        </div>
    </form>
@endsection
