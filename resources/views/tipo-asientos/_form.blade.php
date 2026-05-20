@csrf

@include('partials.validation-errors')

<div class="grid gap-5 md:grid-cols-2">
    <div>
        <label for="cooperativa_id" class="block text-sm font-semibold text-slate-700">Cooperativa</label>
        <select id="cooperativa_id" name="cooperativa_id"
            class="mt-2 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
            <option value="">Todas las cooperativas</option>
            @foreach ($cooperativas as $cooperativa)
                <option value="{{ $cooperativa->id }}" @selected(old('cooperativa_id', $tipoAsiento->cooperativa_id ?? '') == $cooperativa->id)>
                    {{ $cooperativa->nombre }}
                </option>
            @endforeach
        </select>
        @error('cooperativa_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="nombre" class="block text-sm font-semibold text-slate-700">Nombre</label>
        <input id="nombre" name="nombre" type="text" value="{{ old('nombre', $tipoAsiento->nombre ?? '') }}" required
            class="mt-2 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
        @error('nombre')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="recargo" class="block text-sm font-semibold text-slate-700">Recargo</label>
        <input id="recargo" name="recargo" type="number" min="0" max="999999.99" step="0.01"
            value="{{ old('recargo', $tipoAsiento->recargo ?? '0.00') }}" required
            class="mt-2 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
        @error('recargo')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label for="descripcion" class="block text-sm font-semibold text-slate-700">Descripcion</label>
        <textarea id="descripcion" name="descripcion" rows="4"
            class="mt-2 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">{{ old('descripcion', $tipoAsiento->descripcion ?? '') }}</textarea>
        @error('descripcion')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>

<input type="hidden" name="activo" value="0">
<label class="mt-5 flex items-center gap-2 text-sm font-semibold text-slate-700">
    <input type="checkbox" name="activo" value="1" @checked(old('activo', $tipoAsiento->activo ?? true))
        class="rounded border-slate-300">
    Tipo activo
</label>

<div class="mt-6 flex flex-wrap gap-3">
    <button type="submit" class="rounded bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
        Guardar
    </button>
    <a href="{{ route('tipo-asientos.index') }}" class="rounded border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
        Cancelar
    </a>
</div>
