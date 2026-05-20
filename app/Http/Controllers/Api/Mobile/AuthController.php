<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'cedula' => ['nullable', 'string', 'max:10', 'unique:users,cedula'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'cedula' => $validated['cedula'] ?? null,
            'email' => $validated['email'],
            'telefono' => $validated['telefono'] ?? null,
            'password' => $validated['password'],
            'role' => 'cliente',
            'activo' => true,
        ]);

        return response()->json([
            'token' => $this->issueToken($user),
            'user' => $this->userPayload($user),
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password) || ! $user->activo || $user->role !== 'cliente') {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales no son validas.'],
            ]);
        }

        return response()->json([
            'token' => $this->issueToken($user),
            'user' => $this->userPayload($user),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->forceFill(['api_token_hash' => null])->save();

        return response()->json(['message' => 'Sesion cerrada.']);
    }

    private function issueToken(User $user): string
    {
        $token = Str::random(80);

        $user->forceFill([
            'api_token_hash' => hash('sha256', $token),
        ])->save();

        return $token;
    }

    private function userPayload(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'cedula' => $user->cedula,
            'telefono' => $user->telefono,
            'role' => $user->role,
        ];
    }
}
