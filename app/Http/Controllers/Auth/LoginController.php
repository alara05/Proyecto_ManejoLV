<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class LoginController extends Controller
{
    public function show(): View
    {
        return view('auth.login');
    }

    public function web(LoginRequest $request, AuthService $authService): JsonResponse
    {
        try {
            $datos = $authService->loginWeb(
                $request->input('email'),
                $request->input('password'),
                $request->userAgent()
            );

            return $this->jsonOk($datos + [
                'mensaje' => 'Inicio de sesión correcto.',
            ]);
        } catch (RuntimeException $exception) {
            return $this->jsonError($exception->getMessage(), 422);
        }
    }

    public function mobile(LoginRequest $request, AuthService $authService): JsonResponse
    {
        try {
            $datos = $authService->loginMobile(
                $request->input('email'),
                $request->input('password'),
                $request->userAgent()
            );

            return $this->jsonOk($datos + [
                'mensaje' => 'Inicio de sesión mobile correcto.',
            ]);
        } catch (RuntimeException $exception) {
            return $this->jsonError($exception->getMessage(), 422);
        }
    }

    public function options(Request $request): JsonResponse
    {
        return $this->jsonOk([]);
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
