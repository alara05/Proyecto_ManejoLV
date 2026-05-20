@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <p class="text-sm font-medium text-slate-500">Venta interna de oficinista</p>
        <h1 class="text-2xl font-semibold">Vender boleto en oficina</h1>
    </div>

    @include('partials.flash')

    <section class="mb-6 rounded bg-white p-6 shadow-sm">
        <form method="GET" action="{{ route('boletos.create') }}" class="grid gap-5 md:grid-cols-2">
            <div>
                <label for="salida_id" class="block text-sm font-semibold text-slate-700">Salida</label>
                <select id="salida_id" name="salida_id" required
                    class="mt-2 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
                    <option value="">Seleccione una salida</option>
                    @foreach ($salidas as $salidaOption)
                        <option value="{{ $salidaOption->id }}" @selected(request('salida_id') == $salidaOption->id)>
                            {{ $salidaOption->fecha->format('d/m/Y') }}
                            {{ \Illuminate\Support\Str::of($salidaOption->hora_salida)->substr(0, 5) }}
                            / {{ $salidaOption->frecuencia->origen->nombre }} - {{ $salidaOption->frecuencia->destino->nombre }}
                            / Bus {{ $salidaOption->bus->numero }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="tipo_asiento_id" class="block text-sm font-semibold text-slate-700">Tipo de asiento</label>
                <select id="tipo_asiento_id" name="tipo_asiento_id"
                    class="mt-2 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
                    <option value="">Todos</option>
                    @foreach ($tiposAsiento as $tipoAsiento)
                        <option value="{{ $tipoAsiento->id }}" @selected($selectedTipoAsientoId == $tipoAsiento->id)>
                            {{ $tipoAsiento->nombre }}{{ $tipoAsiento->recargo > 0 ? ' + $' . number_format($tipoAsiento->recargo, 2) : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2 flex flex-wrap gap-3">
                <button type="submit" class="rounded bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
                    Ver asientos
                </button>
                <a href="{{ route('boletos.create') }}" class="rounded border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Limpiar
                </a>
            </div>
        </form>
    </section>

    @if ($salida)
        <form method="POST" action="{{ route('boletos.store') }}" class="rounded bg-white p-6 shadow-sm">
            @csrf

            @include('partials.validation-errors')

            <input type="hidden" name="salida_id" value="{{ $salida->id }}">
            <input type="hidden" name="tipo_asiento_id" value="{{ $selectedTipoAsientoId }}">

            <div class="mb-6 rounded border border-slate-200 bg-slate-50 p-4 text-sm">
                <p class="font-semibold text-slate-900">
                    {{ $salida->frecuencia->origen->nombre }} a {{ $salida->frecuencia->destino->nombre }}
                </p>
                <p class="mt-1 text-slate-600">
                    {{ $salida->fecha->format('d/m/Y') }} {{ \Illuminate\Support\Str::of($salida->hora_salida)->substr(0, 5) }}
                    / Bus {{ $salida->bus->numero }}
                    / Precio base ${{ number_format($salida->precio_base, 2) }}
                </p>
            </div>

            <div class="mb-6">
                <h2 class="mb-3 text-lg font-semibold">Asientos disponibles</h2>
                <div class="grid gap-3 sm:grid-cols-3 md:grid-cols-5">
                    @forelse ($asientos as $asiento)
                        @php
                            $ocupado = $asientosOcupados->contains($asiento->id);
                        @endphp
                        <label class="block rounded border p-3 text-sm {{ $ocupado ? 'border-red-200 bg-red-50 text-red-700' : 'border-slate-200 bg-white text-slate-800 hover:border-slate-900' }}">
                            <input type="radio" name="asiento_id" value="{{ $asiento->id }}" @disabled($ocupado) @checked(old('asiento_id') == $asiento->id)
                                class="mr-2">
                            <span class="font-semibold">Asiento {{ $asiento->numero }}</span>
                            <span class="mt-1 block text-xs">
                                {{ $asiento->tipoAsiento->nombre }}
                                {{ $ocupado ? ' / Ocupado' : ' / Disponible' }}
                            </span>
                        </label>
                    @empty
                        <p class="col-span-full rounded border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                            No hay asientos activos para el filtro seleccionado.
                        </p>
                    @endforelse
                </div>
                @error('asiento_id')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label for="pasajero_nombre" class="block text-sm font-semibold text-slate-700">Pasajero</label>
                    <input id="pasajero_nombre" name="pasajero_nombre" type="text" value="{{ old('pasajero_nombre') }}" required
                        class="mt-2 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
                    @error('pasajero_nombre')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="pasajero_cedula" class="block text-sm font-semibold text-slate-700">Cedula</label>
                    <input id="pasajero_cedula" name="pasajero_cedula" type="text" value="{{ old('pasajero_cedula') }}" maxlength="10" required
                        class="mt-2 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
                    @error('pasajero_cedula')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tipo_descuento" class="block text-sm font-semibold text-slate-700">Tipo de descuento</label>
                    <select id="tipo_descuento" name="tipo_descuento" required
                        class="mt-2 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
                        @foreach ($descuentos as $value => $label)
                            <option value="{{ $value }}" @selected(old('tipo_descuento', 'ninguno') === $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('tipo_descuento')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 flex flex-wrap gap-3">
                <button type="submit" class="rounded bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
                    Confirmar venta
                </button>
                <a href="{{ route('boletos.index') }}" class="rounded border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Cancelar
                </a>
            </div>
        </form>
    @endif
@endsection
