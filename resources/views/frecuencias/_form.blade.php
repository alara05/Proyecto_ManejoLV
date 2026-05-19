@csrf

@include('partials.validation-errors')

@php
    $paradasActuales = collect(old('paradas', isset($frecuencia) ? $frecuencia->paradas->map(fn ($parada) => [
        'ciudad_id' => $parada->ciudad_id,
        'minutos_desde_origen' => $parada->minutos_desde_origen,
    ])->toArray() : []))->values();
    $totalFilasParadas = max(5, $paradasActuales->count() + 1);
@endphp

<div class="grid gap-5 md:grid-cols-2">
    <div>
        <label for="cooperativa_id" class="block text-sm font-semibold text-slate-700">Cooperativa</label>
        <select id="cooperativa_id" name="cooperativa_id" required
            class="mt-2 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
            <option value="">Seleccione una cooperativa</option>
            @foreach ($cooperativas as $cooperativa)
                <option value="{{ $cooperativa->id }}" @selected(old('cooperativa_id', $frecuencia->cooperativa_id ?? '') == $cooperativa->id)>
                    {{ $cooperativa->nombre }}
                </option>
            @endforeach
        </select>
        @error('cooperativa_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="hora_salida" class="block text-sm font-semibold text-slate-700">Hora de salida autorizada</label>
        <input id="hora_salida" name="hora_salida" type="time" value="{{ old('hora_salida', isset($frecuencia) ? substr($frecuencia->hora_salida, 0, 5) : '') }}" required
            class="mt-2 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
        @error('hora_salida')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="ciudad_origen_id" class="block text-sm font-semibold text-slate-700">Ciudad origen</label>
        <select id="ciudad_origen_id" name="ciudad_origen_id" required
            class="mt-2 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
            <option value="">Seleccione origen</option>
            @foreach ($ciudades as $ciudad)
                <option value="{{ $ciudad->id }}" @selected(old('ciudad_origen_id', $frecuencia->ciudad_origen_id ?? '') == $ciudad->id)>
                    {{ $ciudad->nombre }} - {{ $ciudad->provincia->nombre }}
                </option>
            @endforeach
        </select>
        @error('ciudad_origen_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="ciudad_destino_id" class="block text-sm font-semibold text-slate-700">Ciudad destino</label>
        <select id="ciudad_destino_id" name="ciudad_destino_id" required
            class="mt-2 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
            <option value="">Seleccione destino</option>
            @foreach ($ciudades as $ciudad)
                <option value="{{ $ciudad->id }}" @selected(old('ciudad_destino_id', $frecuencia->ciudad_destino_id ?? '') == $ciudad->id)>
                    {{ $ciudad->nombre }} - {{ $ciudad->provincia->nombre }}
                </option>
            @endforeach
        </select>
        @error('ciudad_destino_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="numero_resolucion_ant" class="block text-sm font-semibold text-slate-700">Numero de resolucion ANT</label>
        <input id="numero_resolucion_ant" name="numero_resolucion_ant" type="text"
            value="{{ old('numero_resolucion_ant', $frecuencia->numero_resolucion_ant ?? '') }}"
            placeholder="ANT-RES-2026-001"
            class="mt-2 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
        @error('numero_resolucion_ant')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="fecha_resolucion_ant" class="block text-sm font-semibold text-slate-700">Fecha de resolucion ANT</label>
        <input id="fecha_resolucion_ant" name="fecha_resolucion_ant" type="date"
            value="{{ old('fecha_resolucion_ant', isset($frecuencia) && $frecuencia->fecha_resolucion_ant ? $frecuencia->fecha_resolucion_ant->format('Y-m-d') : '') }}"
            class="mt-2 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
        @error('fecha_resolucion_ant')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>

<section class="mt-8 rounded border border-slate-200 bg-slate-50 p-4">
    <div class="mb-4">
        <h2 class="text-base font-semibold text-slate-900">Paradas intermedias</h2>
        <p class="mt-1 text-sm text-slate-500">Registre las paradas en el orden del recorrido. No incluya origen ni destino.</p>
    </div>

    <div class="space-y-3">
        @for ($index = 0; $index < $totalFilasParadas; $index++)
            @php
                $parada = $paradasActuales->get($index, []);
            @endphp
            <div class="grid gap-3 rounded bg-white p-3 shadow-sm md:grid-cols-[1fr_220px]">
                <div>
                    <label for="parada_ciudad_{{ $index }}" class="block text-xs font-semibold text-slate-600">Parada {{ $index + 1 }}</label>
                    <select id="parada_ciudad_{{ $index }}" name="paradas[{{ $index }}][ciudad_id]"
                        class="mt-1 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
                        <option value="">Sin parada</option>
                        @foreach ($ciudades as $ciudad)
                            <option value="{{ $ciudad->id }}" @selected(($parada['ciudad_id'] ?? '') == $ciudad->id)>
                                {{ $ciudad->nombre }} - {{ $ciudad->provincia->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="parada_minutos_{{ $index }}" class="block text-xs font-semibold text-slate-600">Minutos desde origen</label>
                    <input id="parada_minutos_{{ $index }}" name="paradas[{{ $index }}][minutos_desde_origen]" type="number" min="1" max="1440"
                        value="{{ $parada['minutos_desde_origen'] ?? '' }}"
                        class="mt-1 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
                </div>
            </div>
        @endfor
    </div>
</section>

<input type="hidden" name="activa" value="0">
<label class="mt-5 flex items-center gap-2 text-sm font-semibold text-slate-700">
    <input type="checkbox" name="activa" value="1" @checked(old('activa', $frecuencia->activa ?? true))
        class="rounded border-slate-300">
    Frecuencia activa
</label>

<div class="mt-6 flex flex-wrap gap-3">
    <button type="submit" class="rounded bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
        Guardar
    </button>
    <a href="{{ route('frecuencias.index') }}" class="rounded border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
        Cancelar
    </a>
</div>
