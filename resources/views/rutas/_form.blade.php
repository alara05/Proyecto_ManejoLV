@csrf

@include('partials.validation-errors')

<div class="grid gap-5 md:grid-cols-2">
    <div>
        <label for="cooperativa_id" class="block text-sm font-semibold text-slate-700">Cooperativa</label>
        <select id="cooperativa_id" name="cooperativa_id" required
            class="mt-2 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
            <option value="">Seleccione una cooperativa</option>
            @foreach ($cooperativas as $cooperativa)
                <option value="{{ $cooperativa->id }}" @selected(old('cooperativa_id', $ruta->cooperativa_id ?? '') == $cooperativa->id)>
                    {{ $cooperativa->nombre }}
                </option>
            @endforeach
        </select>
        @error('cooperativa_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="bus_id" class="block text-sm font-semibold text-slate-700">Bus asignado</label>
        <select id="bus_id" name="bus_id"
            class="mt-2 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
            <option value="">Sin bus asignado</option>
            @foreach ($buses as $bus)
                <option value="{{ $bus->id }}" @selected(old('bus_id', $ruta->bus_id ?? '') == $bus->id)>
                    Bus {{ $bus->numero }} - {{ $bus->placa }} / {{ $bus->cooperativa->nombre }}
                </option>
            @endforeach
        </select>
        @error('bus_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="ciudad_origen_id" class="block text-sm font-semibold text-slate-700">Ciudad origen</label>
        <select id="ciudad_origen_id" name="ciudad_origen_id" required
            class="mt-2 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
            <option value="">Seleccione origen</option>
            @foreach ($ciudades as $ciudad)
                <option value="{{ $ciudad->id }}" @selected(old('ciudad_origen_id', $ruta->ciudad_origen_id ?? '') == $ciudad->id)>
                    {{ $ciudad->nombre }} - {{ $ciudad->provincia }}
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
                <option value="{{ $ciudad->id }}" @selected(old('ciudad_destino_id', $ruta->ciudad_destino_id ?? '') == $ciudad->id)>
                    {{ $ciudad->nombre }} - {{ $ciudad->provincia }}
                </option>
            @endforeach
        </select>
        @error('ciudad_destino_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="nombre" class="block text-sm font-semibold text-slate-700">Nombre de la ruta</label>
        <input id="nombre" name="nombre" type="text" value="{{ old('nombre', $ruta->nombre ?? '') }}"
            placeholder="Ambato - Quito" required
            class="mt-2 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
        @error('nombre')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="tipo_viaje" class="block text-sm font-semibold text-slate-700">Tipo de viaje</label>
        <select id="tipo_viaje" name="tipo_viaje" required
            class="mt-2 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
            <option value="directo" @selected(old('tipo_viaje', $ruta->tipo_viaje ?? 'directo') === 'directo')>Directo</option>
            <option value="con_paradas" @selected(old('tipo_viaje', $ruta->tipo_viaje ?? '') === 'con_paradas')>Con paradas</option>
        </select>
        @error('tipo_viaje')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="distancia_km" class="block text-sm font-semibold text-slate-700">Distancia KM</label>
        <input id="distancia_km" name="distancia_km" type="number" min="0" step="0.01"
            value="{{ old('distancia_km', $ruta->distancia_km ?? '') }}"
            class="mt-2 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
        @error('distancia_km')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="duracion_minutos" class="block text-sm font-semibold text-slate-700">Duracion estimada en minutos</label>
        <input id="duracion_minutos" name="duracion_minutos" type="number" min="1" max="1440"
            value="{{ old('duracion_minutos', $ruta->duracion_minutos ?? '') }}"
            class="mt-2 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
        @error('duracion_minutos')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>

<input type="hidden" name="activa" value="0">
<label class="mt-5 flex items-center gap-2 text-sm font-semibold text-slate-700">
    <input type="checkbox" name="activa" value="1" @checked(old('activa', $ruta->activa ?? true))
        class="rounded border-slate-300">
    Ruta activa
</label>

<div class="mt-6 flex flex-wrap gap-3">
    <button type="submit" class="rounded bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
        Guardar
    </button>
    <a href="{{ route('rutas.index') }}" class="rounded border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
        Cancelar
    </a>
</div>
