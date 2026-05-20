<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class EncomiendaController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => [],
            'message' => 'El modulo de encomiendas aun no tiene registros disponibles.',
        ]);
    }
}
