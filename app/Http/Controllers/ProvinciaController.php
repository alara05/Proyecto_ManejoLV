<?php

namespace App\Http\Controllers;

use App\Models\Provincia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProvinciaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $provincias = Provincia::withCount('ciudades')
            ->latest()
            ->paginate(10);

        return view('provincias.index', compact('provincias'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('provincias.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        Provincia::create($this->validateProvincia($request));

        return redirect()
            ->route('provincias.index')
            ->with('success', 'Provincia registrada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Provincia $provincia): View
    {
        $provincia->loadCount('ciudades');
        $ciudades = $provincia->ciudades()
            ->orderBy('nombre')
            ->get();

        return view('provincias.show', compact('provincia', 'ciudades'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Provincia $provincia): View
    {
        return view('provincias.edit', compact('provincia'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Provincia $provincia): RedirectResponse
    {
        $provincia->update($this->validateProvincia($request, $provincia));

        return redirect()
            ->route('provincias.index')
            ->with('success', 'Provincia actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Provincia $provincia): RedirectResponse
    {
        if ($provincia->ciudades()->exists()) {
            return redirect()
                ->route('provincias.index')
                ->with('error', 'No se puede eliminar la provincia porque tiene ciudades asociadas.');
        }

        $provincia->delete();

        return redirect()
            ->route('provincias.index')
            ->with('success', 'Provincia eliminada correctamente.');
    }

    private function validateProvincia(Request $request, ?Provincia $provincia = null): array
    {
        $provinciaId = $provincia?->id;

        $validated = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('provincias', 'nombre')->ignore($provinciaId),
            ],
            'activa' => ['nullable', 'boolean'],
        ]);

        $validated['activa'] = $request->boolean('activa');

        return $validated;
    }
}
