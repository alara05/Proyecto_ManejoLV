<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MobileTokenAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (! $token) {
            return response()->json(['message' => 'Token requerido.'], 401);
        }

        $user = User::where('api_token_hash', hash('sha256', $token))->first();

        if (! $user || ! $user->activo || $user->role !== 'cliente') {
            return response()->json(['message' => 'Token invalido.'], 401);
        }

        auth()->setUser($user);
        $request->setUserResolver(fn () => $user);

        return $next($request);
    }
}
