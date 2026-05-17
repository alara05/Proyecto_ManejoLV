<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use RuntimeException;

class RegisterController extends Controller
{
    public function mobile(RegisterRequest $request, AuthService $authService): JsonResponse
    {
        try {
            $datos = $authService->registrarPasajero(
                $request->validated(),
                $request->userAgent()
            );

            return $this->jsonOk($datos + [
                'mensaje' => 'Cuenta creada correctamente.',
            ], 201);
        } catch (RuntimeException $exception) {
            return $this->jsonError($exception->getMessage(), 422);
        }
    }

    private function jsonOk(array $datos, int $estado = 200): JsonResponse
    {
        return response()->json($datos, $estado)->withHeaders($this->corsHeaders());
    }

    private function jsonError(string $mensaje, int $estado): JsonResponse
    {
        return response()->json([
            'ok' => false,
            'mensaje' => $mensaje,
        ], $estado)->withHeaders($this->corsHeaders());
    }

    private function corsHeaders(): array
    {
        return [
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, PATCH, DELETE, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN, Accept',
        ];
    }
}
