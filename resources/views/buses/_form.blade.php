@csrf

<div class="grid gap-5 md:grid-cols-2">
    <div>
        <label for="cooperativa_id" class="block text-sm font-medium">Cooperativa</label>
        <select id="cooperativa_id" name="cooperativa_id" required
            class="mt-2 w-full rounded border border-slate-300 px-3 py-2 focus:border-slate-900 focus:outline-none">
            <option value="">Seleccione una cooperativa</option>
            @foreach ($cooperativas as $cooperativa)
                <option value="{{ $cooperativa->id }}" @selected(old('cooperativa_id', $bus->cooperativa_id ?? '') == $cooperativa->id)>
                    {{ $cooperativa->nombre }}
                </option>
            @endforeach
        </select>
        @error('cooperativa_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="numero" class="block text-sm font-medium">Numero de bus</label>
        <input id="numero" name="numero" type="text" value="{{ old('numero', $bus->numero ?? '') }}" required
            class="mt-2 w-full rounded border border-slate-300 px-3 py-2 focus:border-slate-900 focus:outline-none">
        @error('numero')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="placa" class="block text-sm font-medium">Placa</label>
        <input id="placa" name="placa" type="text" value="{{ old('placa', $bus->placa ?? '') }}" maxlength="10" required
            class="mt-2 w-full rounded uppercase border border-slate-300 px-3 py-2 focus:border-slate-900 focus:outline-none">
        @error('placa')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="capacidad_total" class="block text-sm font-medium">Capacidad total</label>
        <input id="capacidad_total" name="capacidad_total" type="number" min="1" max="100"
            value="{{ old('capacidad_total', $bus->capacidad_total ?? 40) }}" required
            class="mt-2 w-full rounded border border-slate-300 px-3 py-2 focus:border-slate-900 focus:outline-none">
        @error('capacidad_total')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="marca_chasis" class="block text-sm font-medium">Marca del chasis</label>
        <input id="marca_chasis" name="marca_chasis" type="text" value="{{ old('marca_chasis', $bus->marca_chasis ?? '') }}"
            class="mt-2 w-full rounded border border-slate-300 px-3 py-2 focus:border-slate-900 focus:outline-none">
        @error('marca_chasis')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="marca_carroceria" class="block text-sm font-medium">Marca de carroceria</label>
        <input id="marca_carroceria" name="marca_carroceria" type="text" value="{{ old('marca_carroceria', $bus->marca_carroceria ?? '') }}"
            class="mt-2 w-full rounded border border-slate-300 px-3 py-2 focus:border-slate-900 focus:outline-none">
        @error('marca_carroceria')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="anio" class="block text-sm font-medium">Anio</label>
        <input id="anio" name="anio" type="number" min="1980" max="{{ date('Y') + 1 }}" value="{{ old('anio', $bus->anio ?? '') }}"
            class="mt-2 w-full rounded border border-slate-300 px-3 py-2 focus:border-slate-900 focus:outline-none">
        @error('anio')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="foto_path" class="block text-sm font-medium">Ruta de fotografia</label>
        <input id="foto_path" name="foto_path" type="text" value="{{ old('foto_path', $bus->foto_path ?? '') }}"
            placeholder="images/buses/bus-01.jpg"
            class="mt-2 w-full rounded border border-slate-300 px-3 py-2 focus:border-slate-900 focus:outline-none">
        @error('foto_path')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>

<input type="hidden" name="activo" value="0">
<label class="mt-5 flex items-center gap-2 text-sm font-medium">
    <input type="checkbox" name="activo" value="1" @checked(old('activo', $bus->activo ?? true))
        class="rounded border-slate-300">
    Bus activo
</label>

<div class="mt-6 flex flex-wrap gap-3">
    <button type="submit" class="rounded bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">
        Guardar
    </button>
    <a href="{{ route('buses.index') }}" class="rounded border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
        Cancelar
    </a>
</div>
