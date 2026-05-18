@csrf

@include('partials.validation-errors')

<div class="grid gap-5 md:grid-cols-2">
    <div>
        <label for="nombre" class="block text-sm font-semibold text-slate-700">Nombre</label>
        <input id="nombre" name="nombre" type="text" value="{{ old('nombre', $cooperativa->nombre ?? '') }}" required
            class="mt-2 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
        @error('nombre')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="ruc" class="block text-sm font-semibold text-slate-700">RUC</label>
        <input id="ruc" name="ruc" type="text" value="{{ old('ruc', $cooperativa->ruc ?? '') }}" maxlength="13"
            placeholder="13 digitos"
            class="mt-2 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
        @error('ruc')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="telefono" class="block text-sm font-semibold text-slate-700">Telefono</label>
        <input id="telefono" name="telefono" type="text" value="{{ old('telefono', $cooperativa->telefono ?? '') }}" maxlength="20"
            class="mt-2 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
        @error('telefono')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="email" class="block text-sm font-semibold text-slate-700">Correo</label>
        <input id="email" name="email" type="email" value="{{ old('email', $cooperativa->email ?? '') }}"
            class="mt-2 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
        @error('email')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label for="direccion" class="block text-sm font-semibold text-slate-700">Direccion</label>
        <input id="direccion" name="direccion" type="text" value="{{ old('direccion', $cooperativa->direccion ?? '') }}"
            class="mt-2 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
        @error('direccion')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label for="logo_path" class="block text-sm font-semibold text-slate-700">Ruta del logo</label>
        <input id="logo_path" name="logo_path" type="text" value="{{ old('logo_path', $cooperativa->logo_path ?? '') }}"
            placeholder="images/cooperativas/logo.png"
            class="mt-2 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
        @error('logo_path')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>

<input type="hidden" name="activa" value="0">
<label class="mt-5 flex items-center gap-2 text-sm font-semibold text-slate-700">
    <input type="checkbox" name="activa" value="1" @checked(old('activa', $cooperativa->activa ?? true))
        class="rounded border-slate-300">
    Cooperativa activa
</label>

<div class="mt-6 flex flex-wrap gap-3">
    <button type="submit" class="rounded bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
        Guardar
    </button>
    <a href="{{ route('cooperativas.index') }}" class="rounded border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
        Cancelar
    </a>
</div>
