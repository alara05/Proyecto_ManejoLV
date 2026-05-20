<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Asiento;
use App\Models\Boleto;
use App\Models\Pago;
use App\Models\Salida;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TicketController extends Controller
{
    use MobilePayloads;

    public function index(Request $request): JsonResponse
    {
        $boletos = Boleto::with([
            'salida.frecuencia.origen',
            'salida.frecuencia.destino',
            'salida.bus',
            'asiento',
            'pago',
        ])
            ->where(function ($query) use ($request): void {
                $query->where('user_id', $request->user()->id)
                    ->orWhere('cliente_email', $request->user()->email);
            })
            ->latest('vendido_at')
            ->get();

        return response()->json([
            'data' => $boletos->map(fn (Boleto $boleto) => $this->ticketPayload($boleto))->values(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'salida_id' => ['required', 'exists:salidas,id'],
            'asiento_id' => ['required', 'exists:asientos,id'],
            'pasajero_nombre' => ['required', 'string', 'max:255'],
            'pasajero_cedula' => ['required', 'string', 'size:10'],
            'tipo_descuento' => ['nullable', 'in:ninguno,menor_edad,discapacidad,tercera_edad'],
            'metodo_pago' => ['nullable', 'in:tarjeta,transferencia,deposito'],
        ]);

        try {
            $boleto = DB::transaction(function () use ($validated, $request): Boleto {
                $salida = Salida::with('frecuencia')->lockForUpdate()->findOrFail($validated['salida_id']);
                $asiento = Asiento::with('tipoAsiento')->lockForUpdate()->findOrFail($validated['asiento_id']);

                abort_unless($salida->estado === 'programada', 422, 'La salida no esta disponible.');
                abort_unless((int) $asiento->bus_id === (int) $salida->bus_id && $asiento->activo, 422, 'El asiento no pertenece a esta salida.');
                abort_if(Boleto::where('salida_id', $salida->id)->where('asiento_id', $asiento->id)->exists(), 422, 'El asiento ya esta ocupado.');

                $porcentajeDescuento = $this->discountPercentage($validated['tipo_descuento'] ?? 'ninguno');
                $subtotal = (float) $salida->precio_base + (float) ($asiento->tipoAsiento?->recargo ?? 0);
                $precio = round($subtotal - ($subtotal * ($porcentajeDescuento / 100)), 2);

                $boleto = Boleto::create([
                    'salida_id' => $salida->id,
                    'user_id' => $request->user()->id,
                    'vendido_por' => $request->user()->id,
                    'cliente_email' => $request->user()->email,
                    'asiento_id' => $asiento->id,
                    'ciudad_origen_id' => $salida->frecuencia->ciudad_origen_id,
                    'ciudad_destino_id' => $salida->frecuencia->ciudad_destino_id,
                    'codigo' => $this->generateCodigo(),
                    'pasajero_nombre' => $validated['pasajero_nombre'],
                    'pasajero_cedula' => $validated['pasajero_cedula'],
                    'tipo_descuento' => $validated['tipo_descuento'] ?? 'ninguno',
                    'porcentaje_descuento' => $porcentajeDescuento,
                    'precio' => $precio,
                    'estado' => 'pendiente',
                    'vendido_at' => now(),
                ]);

                if (! empty($validated['metodo_pago'])) {
                    Pago::create([
                        'boleto_id' => $boleto->id,
                        'metodo' => $validated['metodo_pago'],
                        'monto' => $precio,
                        'estado' => 'pendiente',
                        'observacion' => 'Pago iniciado desde app movil.',
                    ]);
                }

                return $boleto;
            });
        } catch (QueryException) {
            return response()->json(['message' => 'El asiento ya esta ocupado.'], 422);
        }

        return response()->json(['data' => $this->ticketPayload($boleto)], 201);
    }

    public function show(Request $request, Boleto $boleto): JsonResponse
    {
        abort_unless($boleto->user_id === $request->user()->id || $boleto->cliente_email === $request->user()->email, 403);

        return response()->json(['data' => $this->ticketPayload($boleto)]);
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
            $codigo = 'APP-'.now()->format('Ymd').'-'.Str::upper(Str::random(6));
        } while (Boleto::where('codigo', $codigo)->exists());

        return $codigo;
    }
}
