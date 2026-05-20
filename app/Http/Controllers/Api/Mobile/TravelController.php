<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Salida;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TravelController extends Controller
{
    use MobilePayloads;

    public function index(Request $request): JsonResponse
    {
        $query = Salida::with([
            'frecuencia.origen',
            'frecuencia.destino',
            'bus.cooperativa',
            'bus.asientos.tipoAsiento',
            'boletos',
        ])
            ->where('estado', 'programada')
            ->where('fecha', '>=', now()->toDateString())
            ->orderBy('fecha')
            ->orderBy('hora_salida');

        if ($request->filled('origin')) {
            $query->whereHas('frecuencia.origen', fn ($q) => $q->where('nombre', 'like', '%'.$request->input('origin').'%'));
        }

        if ($request->filled('destination')) {
            $query->whereHas('frecuencia.destino', fn ($q) => $q->where('nombre', 'like', '%'.$request->input('destination').'%'));
        }

        return response()->json([
            'data' => $query->limit(30)->get()->map(fn (Salida $salida) => $this->travelPayload($salida))->values(),
        ]);
    }

    public function show(Salida $salida): JsonResponse
    {
        abort_unless($salida->estado === 'programada', 404);

        return response()->json(['data' => $this->travelPayload($salida)]);
    }
}
