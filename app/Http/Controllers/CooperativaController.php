<?php

namespace App\Http\Controllers;

use App\Models\Cooperativa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CooperativaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $cooperativas = Cooperativa::withCount(['buses', 'rutas', 'usuarios'])
            ->latest()
            ->paginate(10);

        return view('cooperativas.index', compact('cooperativas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('cooperativas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        Cooperativa::create($this->validateCooperativa($request));

        return redirect()
            ->route('cooperativas.index')
            ->with('success', 'Cooperativa registrada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Cooperativa $cooperativa): View
    {
        $cooperativa->loadCount(['buses', 'rutas', 'usuarios']);

        return view('cooperativas.show', compact('cooperativa'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cooperativa $cooperativa): View
    {
        return view('cooperativas.edit', compact('cooperativa'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cooperativa $cooperativa): RedirectResponse
    {
        $cooperativa->update($this->validateCooperativa($request, $cooperativa));

        return redirect()
            ->route('cooperativas.index')
            ->with('success', 'Cooperativa actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cooperativa $cooperativa): RedirectResponse
    {
        if (
            $cooperativa->buses()->exists()
            || $cooperativa->rutas()->exists()
            || $cooperativa->frecuencias()->exists()
            || $cooperativa->usuarios()->exists()
        ) {
            return redirect()
                ->route('cooperativas.index')
                ->with('error', 'No se puede eliminar la cooperativa porque tiene registros asociados.');
        }

        $cooperativa->delete();

        return redirect()
            ->route('cooperativas.index')
            ->with('success', 'Cooperativa eliminada correctamente.');
    }

    private function validateCooperativa(Request $request, ?Cooperativa $cooperativa = null): array
    {
        $cooperativaId = $cooperativa?->id;

        $validated = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('cooperativas', 'nombre')->ignore($cooperativaId),
            ],
            'ruc' => [
                'nullable',
                'digits:13',
                Rule::unique('cooperativas', 'ruc')->ignore($cooperativaId),
            ],
            'telefono' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'logo_path' => ['nullable', 'string', 'max:255'],
            'activa' => ['nullable', 'boolean'],
        ]);

        $validated['ruc'] = $validated['ruc'] ?? null;
        $validated['email'] = isset($validated['email']) ? strtolower($validated['email']) : null;
        $validated['activa'] = $request->boolean('activa');

        return $validated;
    }
}
