@csrf

@include('partials.validation-errors')

<div class="grid gap-5 md:grid-cols-2">
    <div class="md:col-span-2">
        <label for="nombre" class="block text-sm font-semibold text-slate-700">Nombre</label>
        <input id="nombre" name="nombre" type="text" value="{{ old('nombre', $provincia->nombre ?? '') }}" required
            placeholder="Tungurahua"
            class="mt-2 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
        @error('nombre')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>

<input type="hidden" name="activa" value="0">
<label class="mt-5 flex items-center gap-2 text-sm font-semibold text-slate-700">
    <input type="checkbox" name="activa" value="1" @checked(old('activa', $provincia->activa ?? true))
        class="rounded border-slate-300">
    Provincia activa
</label>

<div class="mt-6 flex flex-wrap gap-3">
    <button type="submit" class="rounded bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
        Guardar
    </button>
    <a href="{{ route('provincias.index') }}" class="rounded border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
        Cancelar
    </a>
</div>
