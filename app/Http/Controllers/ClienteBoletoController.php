<?php

namespace App\Http\Controllers;

use App\Models\Asiento;
use App\Models\Boleto;
use App\Models\Salida;
use App\Models\TipoAsiento;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;
use Illuminate\View\View;

class ClienteBoletoController extends Controller
{
    public function create(Request $request): View
    {
        $salida = null;
        $asientos = collect();
        $tiposAsiento = collect();
        $asientosOcupados = collect();

        if ($request->filled('salida_id')) {
            $salida = Salida::with([
                'bus.asientos.tipoAsiento',
                'frecuencia.origen.provincia',
                'frecuencia.destino.provincia',
                'boletos',
            ])
                ->where('estado', 'programada')
                ->find($request->input('salida_id'));

            if ($salida) {
                $asientosOcupados = $salida->boletos->pluck('asiento_id');
                $tiposAsiento = $salida->bus->asientos
                    ->where('activo', true)
                    ->pluck('tipoAsiento')
                    ->filter()
                    ->unique('id')
                    ->sortBy('nombre')
                    ->values();

                $asientos = $salida->bus->asientos
                    ->where('activo', true)
                    ->when($request->filled('tipo_asiento_id'), function ($collection) use ($request) {
                        return $collection->where('tipo_asiento_id', (int) $request->input('tipo_asiento_id'));
                    })
                    ->sortBy('numero')
                    ->values();
            }
        }

        return view('cliente.boletos.create', [
            'salidas' => $this->salidasDisponibles(),
            'salida' => $salida,
            'asientos' => $asientos,
            'tiposAsiento' => $tiposAsiento,
            'asientosOcupados' => $asientosOcupados,
            'selectedTipoAsientoId' => $request->input('tipo_asiento_id'),
            'descuentos' => $this->discountLabels(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateClienteBoleto($request);

        try {
            $boleto = DB::transaction(function () use ($validated) {
                $salida = Salida::with('frecuencia')->lockForUpdate()->findOrFail($validated['salida_id']);
                $asiento = Asiento::with('tipoAsiento')->findOrFail($validated['asiento_id']);

                if (Boleto::where('salida_id', $salida->id)->where('asiento_id', $asiento->id)->exists()) {
                    return null;
                }

                $porcentajeDescuento = $this->discountPercentage($validated['tipo_descuento']);
                $subtotal = (float) $salida->precio_base + (float) ($asiento->tipoAsiento?->recargo ?? 0);
                $precio = round($subtotal - ($subtotal * ($porcentajeDescuento / 100)), 2);

                return Boleto::create([
                    'salida_id' => $salida->id,
                    'user_id' => auth()->id(),
                    'asiento_id' => $asiento->id,
                    'ciudad_origen_id' => $salida->frecuencia->ciudad_origen_id,
                    'ciudad_destino_id' => $salida->frecuencia->ciudad_destino_id,
                    'codigo' => $this->generateCodigo(),
                    'pasajero_nombre' => $validated['pasajero_nombre'],
                    'pasajero_cedula' => $validated['pasajero_cedula'],
                    'tipo_descuento' => $validated['tipo_descuento'],
                    'porcentaje_descuento' => $porcentajeDescuento,
                    'precio' => $precio,
                    'estado' => 'reservado',
                    'vendido_at' => null,
                ]);
            });
        } catch (QueryException $exception) {
            return back()
                ->withInput()
                ->withErrors(['asiento_id' => 'El asiento seleccionado ya esta ocupado para esta salida.']);
        }

        if (! $boleto) {
            return back()
                ->withInput()
                ->withErrors(['asiento_id' => 'El asiento seleccionado ya esta ocupado para esta salida.']);
        }

        return redirect()
            ->route('cliente.boletos.show', $boleto)
            ->with('success', 'Boleto reservado correctamente.');
    }

    public function show(Boleto $boleto): View
    {
        $boleto->load([
            'salida.frecuencia.origen.provincia',
            'salida.frecuencia.destino.provincia',
            'salida.bus',
            'asiento.tipoAsiento',
        ]);

        return view('cliente.boletos.show', compact('boleto'));
    }

    private function salidasDisponibles()
    {
        return Salida::with(['frecuencia.origen', 'frecuencia.destino', 'bus'])
            ->where('estado', 'programada')
            ->where('fecha', '>=', now()->toDateString())
            ->orderBy('fecha')
            ->orderBy('hora_salida')
            ->get();
    }

    private function validateClienteBoleto(Request $request): array
    {
        $validator = validator($request->all(), [
            'salida_id' => ['required', 'exists:salidas,id'],
            'tipo_asiento_id' => ['nullable', 'exists:tipo_asientos,id'],
            'asiento_id' => ['required', 'exists:asientos,id'],
            'pasajero_nombre' => ['required', 'string', 'max:255'],
            'pasajero_cedula' => ['required', 'string', 'size:10'],
            'tipo_descuento' => ['required', 'in:ninguno,menor_edad,discapacidad,tercera_edad'],
        ]);

        $validator->after(function (Validator $validator) use ($request): void {
            $salida = Salida::find($request->input('salida_id'));
            $asiento = Asiento::find($request->input('asiento_id'));
            $tipoAsiento = $request->filled('tipo_asiento_id')
                ? TipoAsiento::find($request->input('tipo_asiento_id'))
                : null;

            if (! $salida || ! $asiento) {
                return;
            }

            if ($salida->estado !== 'programada') {
                $validator->errors()->add('salida_id', 'Solo se pueden comprar boletos de salidas programadas.');
            }

            if (! $asiento->activo || (int) $asiento->bus_id !== (int) $salida->bus_id) {
                $validator->errors()->add('asiento_id', 'El asiento no pertenece al bus asignado a la salida.');
            }

            if ($tipoAsiento && (int) $asiento->tipo_asiento_id !== (int) $tipoAsiento->id) {
                $validator->errors()->add('asiento_id', 'El asiento seleccionado no corresponde al tipo filtrado.');
            }

            if (Boleto::where('salida_id', $salida->id)->where('asiento_id', $asiento->id)->exists()) {
                $validator->errors()->add('asiento_id', 'El asiento seleccionado ya esta ocupado para esta salida.');
            }
        });

        return $validator->validate();
    }

    private function discountLabels(): array
    {
        return [
            'ninguno' => 'Sin descuento',
            'menor_edad' => 'Menor de edad - 50%',
            'discapacidad' => 'Discapacidad - 50%',
            'tercera_edad' => 'Tercera edad - 50%',
        ];
    }

    private function discountPercentage(string $tipoDescuento): float
    {
        return match ($tipoDescuento) {
            'menor_edad', 'discapacidad', 'tercera_edad' => 50,
            default => 0,
        };
    }

    private function generateCodigo(): string
    {
        do {
            $codigo = 'BOL-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
        } while (Boleto::where('codigo', $codigo)->exists());

        return $codigo;
    }
}
