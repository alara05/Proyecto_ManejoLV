<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        return response()->json(['data' => $this->payload($request)]);
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'cedula' => ['nullable', 'string', 'max:10', 'unique:users,cedula,'.$request->user()->id],
        ]);

        $request->user()->update($validated);

        return response()->json(['data' => $this->payload($request)]);
    }

    private function payload(Request $request): array
    {
        $user = $request->user();

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
