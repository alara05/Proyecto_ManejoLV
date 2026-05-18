@csrf

@include('partials.validation-errors')

<div class="grid gap-5 md:grid-cols-2">
    <div>
        <label for="bus_id" class="block text-sm font-semibold text-slate-700">Bus</label>
        <select id="bus_id" name="bus_id" required
            class="mt-2 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
            <option value="">Seleccione un bus</option>
            @foreach ($buses as $bus)
                <option value="{{ $bus->id }}" @selected(old('bus_id', $asiento->bus_id ?? '') == $bus->id)>
                    Bus {{ $bus->numero }} - {{ $bus->cooperativa->nombre }} - {{ $bus->placa }}
                </option>
            @endforeach
        </select>
        @error('bus_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="tipo_asiento_id" class="block text-sm font-semibold text-slate-700">Tipo de asiento</label>
        <select id="tipo_asiento_id" name="tipo_asiento_id" required
            class="mt-2 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
            <option value="">Seleccione un tipo</option>
            @foreach ($tipoAsientos as $tipoAsiento)
                <option value="{{ $tipoAsiento->id }}" @selected(old('tipo_asiento_id', $asiento->tipo_asiento_id ?? '') == $tipoAsiento->id)>
                    {{ $tipoAsiento->nombre }} - {{ $tipoAsiento->cooperativa->nombre ?? 'Todas' }}
                </option>
            @endforeach
        </select>
        @error('tipo_asiento_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="numero" class="block text-sm font-semibold text-slate-700">Numero de asiento</label>
        <input id="numero" name="numero" type="text" value="{{ old('numero', $asiento->numero ?? '') }}" maxlength="20" required
            class="mt-2 w-full rounded border border-slate-300 px-3 py-2 text-sm uppercase focus:border-slate-900 focus:outline-none">
        @error('numero')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>

<input type="hidden" name="activo" value="0">
<label class="mt-5 flex items-center gap-2 text-sm font-semibold text-slate-700">
    <input type="checkbox" name="activo" value="1" @checked(old('activo', $asiento->activo ?? true))
        class="rounded border-slate-300">
    Asiento activo
</label>

<div class="mt-6 flex flex-wrap gap-3">
    <button type="submit" class="rounded bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
        Guardar
    </button>
    <a href="{{ route('asientos.index') }}" class="rounded border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
        Cancelar
    </a>
</div>
