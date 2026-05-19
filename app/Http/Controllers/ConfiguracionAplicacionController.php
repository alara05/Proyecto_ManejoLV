<?php

namespace App\Http\Controllers;

use App\Models\ConfiguracionAplicacion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ConfiguracionAplicacionController extends Controller
{
    public function edit(): View
    {
        $this->authorizeConfiguration();

        return view('configuracion.edit', [
            'configuracion' => $this->configuration(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $this->authorizeConfiguration();

        $configuracion = $this->configuration();

        $validated = $request->validate([
            'nombre_aplicacion' => ['required', 'string', 'max:255'],
            'logo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'color_primario' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'color_secundario' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'email_soporte' => ['nullable', 'email', 'max:255'],
            'telefono_soporte' => ['nullable', 'string', 'max:20'],
            'redes_sociales.facebook' => ['nullable', 'url', 'max:255'],
            'redes_sociales.instagram' => ['nullable', 'url', 'max:255'],
            'redes_sociales.x' => ['nullable', 'url', 'max:255'],
            'redes_sociales.whatsapp' => ['nullable', 'url', 'max:255'],
        ]);

        $logoPath = $configuracion->logo_path;

        if ($request->hasFile('logo')) {
            if ($logoPath) {
                Storage::disk('public')->delete($logoPath);
            }

            $logoPath = $request->file('logo')->store('configuracion', 'public');
        }

        $configuracion->update([
            'nombre_aplicacion' => $validated['nombre_aplicacion'],
            'logo_path' => $logoPath,
            'color_primario' => $validated['color_primario'],
            'color_secundario' => $validated['color_secundario'],
            'email_soporte' => $validated['email_soporte'] ?? null,
            'telefono_soporte' => $validated['telefono_soporte'] ?? null,
            'redes_sociales' => array_filter($validated['redes_sociales'] ?? []),
        ]);

        return redirect()
            ->route('configuracion.edit')
            ->with('success', 'Configuracion actualizada correctamente.');
    }

    private function configuration(): ConfiguracionAplicacion
    {
        return ConfiguracionAplicacion::firstOrCreate([], [
            'nombre_aplicacion' => 'Manejo Buses',
            'color_primario' => '#0f172a',
            'color_secundario' => '#f59e0b',
        ]);
    }

    private function authorizeConfiguration(): void
    {
        abort_unless(auth()->user()?->role === 'admin', 403);
    }
}
