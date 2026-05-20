<?php

namespace App\Http\Controllers;

use App\Models\Asiento;
use App\Models\Boleto;
use App\Models\Pago;
use App\Models\Salida;
use App\Models\TipoAsiento;
use App\Services\BoletoNotificationDispatcher;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;
use Illuminate\View\View;

class ClienteBoletoController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        $salida = null;
        $asientos = collect();
        $tiposAsiento = collect();
        $asientosOcupados = collect();
        $salidas = $this->salidasDisponibles();

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
            'salidas' => $salidas,
            'salida' => $salida,
            'asientos' => $asientos,
            'tiposAsiento' => $tiposAsiento,
            'asientosOcupados' => $asientosOcupados,
            'selectedTipoAsientoId' => $request->input('tipo_asiento_id'),
            'descuentos' => $this->discountLabels(),
            'salesData' => $this->salesData($salidas),
            'metodosPago' => $this->paymentMethodLabels(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if (! $request->filled('asiento_ids') && $request->filled('asiento_id')) {
            $request->merge(['asiento_ids' => [$request->input('asiento_id')]]);
        }

        $validated = $this->validateClienteBoleto($request);

        try {
            $boletos = DB::transaction(function () use ($validated) {
                $salida = Salida::with('frecuencia')->lockForUpdate()->findOrFail($validated['salida_id']);
                $porcentajeDescuento = $this->discountPercentage($validated['tipo_descuento']);
                $asientos = Asiento::with('tipoAsiento')
                    ->whereIn('id', $validated['asiento_ids'])
                    ->lockForUpdate()
                    ->get()
                    ->sortBy('numero')
                    ->values();

                if (Boleto::where('salida_id', $salida->id)->whereIn('asiento_id', $asientos->pluck('id'))->exists()) {
                    return collect();
                }

                $created = collect();
                $totalVenta = $this->requestTotal($salida, $asientos, $validated['tipo_descuento']);

                foreach ($asientos as $asiento) {
                    $subtotal = (float) $salida->precio_base + (float) ($asiento->tipoAsiento?->recargo ?? 0);
                    $precio = round($subtotal - ($subtotal * ($porcentajeDescuento / 100)), 2);

                    $boleto = Boleto::create([
                        'salida_id' => $salida->id,
                        'user_id' => auth()->id(),
                        'vendido_por' => auth()->id(),
                        'cliente_email' => $validated['cliente_email'] ?? auth()->user()?->email,
                        'asiento_id' => $asiento->id,
                        'ciudad_origen_id' => $salida->frecuencia->ciudad_origen_id,
                        'ciudad_destino_id' => $salida->frecuencia->ciudad_destino_id,
                        'codigo' => $this->generateCodigo(),
                        'pasajero_nombre' => $validated['pasajero_nombre'],
                        'pasajero_cedula' => $validated['pasajero_cedula'],
                        'tipo_descuento' => $validated['tipo_descuento'],
                        'porcentaje_descuento' => $porcentajeDescuento,
                        'precio' => $precio,
                        'estado' => 'pagado',
                        'vendido_at' => now(),
                    ]);

                    Pago::create([
                        'boleto_id' => $boleto->id,
                        'validado_por' => auth()->id(),
                        'metodo' => $validated['metodo_pago'],
                        'monto' => $precio,
                        'estado' => 'validado',
                        'validado_at' => now(),
                        'observacion' => $this->paymentObservation($validated, $totalVenta),
                    ]);

                    $created->push($boleto);
                }

                return $created;
            });
        } catch (QueryException $exception) {
            return back()
                ->withInput()
                ->withErrors([
                    'asiento_id' => 'Uno de los asientos seleccionados ya esta ocupado para esta salida.',
                    'asiento_ids' => 'Uno de los asientos seleccionados ya esta ocupado para esta salida.',
                ]);
        }

        if ($boletos->isEmpty()) {
            return back()
                ->withInput()
                ->withErrors([
                    'asiento_id' => 'Uno de los asientos seleccionados ya esta ocupado para esta salida.',
                    'asiento_ids' => 'Uno de los asientos seleccionados ya esta ocupado para esta salida.',
                ]);
        }

        $boletos->each(fn (Boleto $boleto) => app(BoletoNotificationDispatcher::class)->compraRegistrada($boleto));

        return redirect()
            ->route('cliente.boletos.show', $boletos->first())
            ->with('success', $boletos->count() === 1 ? 'Boleto vendido correctamente.' : $boletos->count().' boletos vendidos correctamente.');
    }

    public function show(Boleto $boleto): View
    {
        $boleto->load([
            'salida.frecuencia.origen.provincia',
            'salida.frecuencia.destino.provincia',
            'salida.bus',
            'asiento.tipoAsiento',
            'pago',
        ]);

        return view('cliente.boletos.show', compact('boleto'));
    }

    private function salidasDisponibles()
    {
        return Salida::with([
            'frecuencia.origen.provincia',
            'frecuencia.destino.provincia',
            'bus.cooperativa',
            'bus.asientos.tipoAsiento',
            'boletos',
        ])
            ->where('estado', 'programada')
            ->where('fecha', '>=', now()->toDateString())
            ->orderBy('fecha')
            ->orderBy('hora_salida')
            ->get();
    }

    private function validateClienteBoleto(Request $request): array
    {
        $clienteEmailRules = [auth()->check() ? 'nullable' : 'required', 'email', 'max:255'];

        $validator = validator($request->all(), [
            'salida_id' => ['required', 'exists:salidas,id'],
            'tipo_asiento_id' => ['nullable', 'exists:tipo_asientos,id'],
            'asiento_ids' => ['required', 'array', 'min:1'],
            'asiento_ids.*' => ['required', 'integer', 'distinct', 'exists:asientos,id'],
            'cliente_email' => $clienteEmailRules,
            'pasajero_nombre' => ['required', 'string', 'max:255'],
            'pasajero_cedula' => ['required', 'string', 'size:10'],
            'tipo_descuento' => ['required', 'in:ninguno,menor_edad,discapacidad,tercera_edad'],
            'metodo_pago' => ['required', 'in:efectivo,tarjeta,transferencia,deposito'],
            'comprobante_tipo' => ['required', 'in:ticket,factura'],
            'efectivo_recibido' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'tarjeta_titular' => ['nullable', 'string', 'max:120'],
            'tarjeta_ultimos4' => ['nullable', 'digits:4'],
            'tarjeta_autorizacion' => ['nullable', 'string', 'max:60'],
            'tarjeta_marca' => ['nullable', 'string', 'max:40'],
            'transferencia_banco' => ['nullable', 'string', 'max:120'],
            'transferencia_referencia' => ['nullable', 'string', 'max:80'],
            'transferencia_titular' => ['nullable', 'string', 'max:120'],
            'deposito_banco' => ['nullable', 'string', 'max:120'],
            'deposito_numero' => ['nullable', 'string', 'max:80'],
            'deposito_titular' => ['nullable', 'string', 'max:120'],
            'observacion_pago' => ['nullable', 'string', 'max:500'],
        ]);

        $validator->after(function (Validator $validator) use ($request): void {
            $salida = Salida::find($request->input('salida_id'));
            $asientos = Asiento::with('tipoAsiento')
                ->whereIn('id', $request->input('asiento_ids', []))
                ->get();
            $tipoAsiento = $request->filled('tipo_asiento_id')
                ? TipoAsiento::find($request->input('tipo_asiento_id'))
                : null;

            if (! $salida || $asientos->isEmpty()) {
                return;
            }

            if ($salida->estado !== 'programada') {
                $validator->errors()->add('salida_id', 'Solo se pueden comprar boletos de salidas programadas.');
            }

            foreach ($asientos as $asiento) {
                if (! $asiento->activo || (int) $asiento->bus_id !== (int) $salida->bus_id) {
                    $validator->errors()->add('asiento_id', 'Todos los asientos deben pertenecer al bus asignado a la salida.');
                    $validator->errors()->add('asiento_ids', 'Todos los asientos deben pertenecer al bus asignado a la salida.');
                    break;
                }

                if ($tipoAsiento && (int) $asiento->tipo_asiento_id !== (int) $tipoAsiento->id) {
                    $validator->errors()->add('asiento_id', 'Todos los asientos seleccionados deben corresponder al tipo filtrado.');
                    $validator->errors()->add('asiento_ids', 'Todos los asientos seleccionados deben corresponder al tipo filtrado.');
                    break;
                }
            }

            if (Boleto::where('salida_id', $salida->id)->whereIn('asiento_id', $asientos->pluck('id'))->exists()) {
                $validator->errors()->add('asiento_id', 'Uno de los asientos seleccionados ya esta ocupado para esta salida.');
                $validator->errors()->add('asiento_ids', 'Uno de los asientos seleccionados ya esta ocupado para esta salida.');
            }

            $total = $this->requestTotal($salida, $asientos, (string) $request->input('tipo_descuento', 'ninguno'));
            $metodoPago = $request->input('metodo_pago');

            if ($metodoPago === 'efectivo' && (float) $request->input('efectivo_recibido', 0) < $total) {
                $validator->errors()->add('efectivo_recibido', 'El efectivo recibido debe cubrir el total de la venta.');
            }

            foreach ($this->requiredPaymentFields($metodoPago) as $field => $label) {
                if (! $request->filled($field)) {
                    $validator->errors()->add($field, "El campo {$label} es obligatorio para este metodo de pago.");
                }
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
            $codigo = 'BOL-'.now()->format('Ymd').'-'.Str::upper(Str::random(6));
        } while (Boleto::where('codigo', $codigo)->exists());

        return $codigo;
    }

    private function salesData($salidas): array
    {
        return $salidas->map(function (Salida $salida): array {
            $ocupados = $salida->boletos->pluck('asiento_id')->all();

            return [
                'id' => $salida->id,
                'destinoKey' => $salida->frecuencia->ciudad_origen_id.'-'.$salida->frecuencia->ciudad_destino_id,
                'destinoLabel' => $salida->frecuencia->origen->nombre.' - '.$salida->frecuencia->destino->nombre,
                'origen' => $salida->frecuencia->origen->nombre,
                'destino' => $salida->frecuencia->destino->nombre,
                'fecha' => $salida->fecha->format('d/m/Y'),
                'hora' => Str::of($salida->hora_salida)->substr(0, 5)->toString(),
                'precioBase' => (float) $salida->precio_base,
                'busId' => $salida->bus->id,
                'busLabel' => 'Bus '.$salida->bus->numero.' / '.$salida->bus->placa,
                'cooperativa' => $salida->bus->cooperativa->nombre ?? 'Sin cooperativa',
                'asientos' => $salida->bus->asientos
                    ->where('activo', true)
                    ->sortBy(fn (Asiento $asiento) => (int) preg_replace('/\D+/', '', $asiento->numero))
                    ->values()
                    ->map(function (Asiento $asiento) use ($ocupados, $salida): array {
                        $recargo = (float) ($asiento->tipoAsiento?->recargo ?? 0);

                        return [
                            'id' => $asiento->id,
                            'numero' => $asiento->numero,
                            'tipoId' => $asiento->tipo_asiento_id,
                            'tipo' => $asiento->tipoAsiento->nombre ?? 'General',
                            'recargo' => $recargo,
                            'precio' => round((float) $salida->precio_base + $recargo, 2),
                            'ocupado' => in_array($asiento->id, $ocupados, true),
                        ];
                    })
                    ->all(),
            ];
        })->values()->all();
    }

    private function requestTotal(Salida $salida, $asientos, string $tipoDescuento): float
    {
        $porcentajeDescuento = $this->discountPercentage($tipoDescuento);

        return round($asientos->sum(function (Asiento $asiento) use ($salida, $porcentajeDescuento): float {
            $subtotal = (float) $salida->precio_base + (float) ($asiento->tipoAsiento?->recargo ?? 0);

            return round($subtotal - ($subtotal * ($porcentajeDescuento / 100)), 2);
        }), 2);
    }

    private function paymentMethodLabels(): array
    {
        return [
            'efectivo' => 'Efectivo',
            'tarjeta' => 'Tarjeta',
            'transferencia' => 'Transferencia bancaria',
            'deposito' => 'Deposito bancario',
        ];
    }

    private function requiredPaymentFields(?string $metodoPago): array
    {
        return match ($metodoPago) {
            'efectivo' => ['efectivo_recibido' => 'efectivo recibido'],
            'tarjeta' => [
                'tarjeta_titular' => 'titular de la tarjeta',
                'tarjeta_ultimos4' => 'ultimos 4 digitos',
                'tarjeta_autorizacion' => 'codigo de autorizacion',
            ],
            'transferencia' => [
                'transferencia_banco' => 'banco emisor',
                'transferencia_referencia' => 'referencia',
                'transferencia_titular' => 'titular de la cuenta',
            ],
            'deposito' => [
                'deposito_banco' => 'banco receptor',
                'deposito_numero' => 'numero de deposito',
                'deposito_titular' => 'depositante',
            ],
            default => [],
        };
    }

    private function paymentObservation(array $validated, float $totalVenta): string
    {
        $base = [
            'Comprobante: '.strtoupper($validated['comprobante_tipo']),
            'Total venta agrupada: $'.number_format($totalVenta, 2),
        ];

        $base[] = match ($validated['metodo_pago']) {
            'efectivo' => 'Efectivo recibido: $'.number_format((float) $validated['efectivo_recibido'], 2).' / Cambio: $'.number_format(max((float) $validated['efectivo_recibido'] - $totalVenta, 0), 2),
            'tarjeta' => 'Tarjeta '.($validated['tarjeta_marca'] ?? 'no especificada').' / Titular: '.$validated['tarjeta_titular'].' / **** '.$validated['tarjeta_ultimos4'].' / Autorizacion: '.$validated['tarjeta_autorizacion'],
            'transferencia' => 'Transferencia / Banco: '.$validated['transferencia_banco'].' / Titular: '.$validated['transferencia_titular'].' / Referencia: '.$validated['transferencia_referencia'],
            'deposito' => 'Deposito / Banco: '.$validated['deposito_banco'].' / Depositante: '.$validated['deposito_titular'].' / Numero: '.$validated['deposito_numero'],
            default => 'Metodo no especificado',
        };

        if (! empty($validated['observacion_pago'])) {
            $base[] = 'Observacion: '.$validated['observacion_pago'];
        }

        return implode(' | ', $base);
    }
}
