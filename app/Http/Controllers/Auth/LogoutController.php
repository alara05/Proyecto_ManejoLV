<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function logout(Request $request, AuthService $authService): JsonResponse
    {
        $token = $request->bearerToken() ?: $request->input('token');
        $authService->cerrarSesion($token);

        return response()->json([
            'ok' => true,
            'mensaje' => 'Sesión cerrada correctamente.',
        ])->withHeaders([
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, PATCH, DELETE, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN, Accept',
        ]);
    }
}
