<?php

namespace App\Http\Controllers;

use App\Models\Ciudad;
use App\Models\Provincia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CiudadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $ciudades = Ciudad::with('provincia')
            ->withCount([
                'rutasOrigen',
                'rutasDestino',
                'frecuenciasOrigen',
                'frecuenciasDestino',
                'paradas',
            ])
            ->latest()
            ->paginate(10);

        return view('ciudades.index', compact('ciudades'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('ciudades.create', $this->formData());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        Ciudad::create($this->validateCiudad($request));

        return redirect()
            ->route('ciudades.index')
            ->with('success', 'Ciudad registrada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Ciudad $ciudad): View
    {
        $ciudad->load('provincia')->loadCount([
            'rutasOrigen',
            'rutasDestino',
            'frecuenciasOrigen',
            'frecuenciasDestino',
            'paradas',
            'boletosOrigen',
            'boletosDestino',
        ]);

        return view('ciudades.show', compact('ciudad'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ciudad $ciudad): View
    {
        return view('ciudades.edit', ['ciudad' => $ciudad] + $this->formData());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ciudad $ciudad): RedirectResponse
    {
        $ciudad->update($this->validateCiudad($request, $ciudad));

        return redirect()
            ->route('ciudades.index')
            ->with('success', 'Ciudad actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ciudad $ciudad): RedirectResponse
    {
        if (
            $ciudad->rutasOrigen()->exists()
            || $ciudad->rutasDestino()->exists()
            || $ciudad->frecuenciasOrigen()->exists()
            || $ciudad->frecuenciasDestino()->exists()
            || $ciudad->paradas()->exists()
            || $ciudad->boletosOrigen()->exists()
            || $ciudad->boletosDestino()->exists()
        ) {
            return redirect()
                ->route('ciudades.index')
                ->with('error', 'No se puede eliminar la ciudad porque esta usada como origen, destino o parada.');
        }

        $ciudad->delete();

        return redirect()
            ->route('ciudades.index')
            ->with('success', 'Ciudad eliminada correctamente.');
    }

    private function formData(): array
    {
        return [
            'provincias' => Provincia::where('activa', true)->orderBy('nombre')->get(),
        ];
    }

    private function validateCiudad(Request $request, ?Ciudad $ciudad = null): array
    {
        $ciudadId = $ciudad?->id;

        $validated = $request->validate([
            'provincia_id' => ['required', 'exists:provincias,id'],
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('ciudades', 'nombre')
                    ->where('provincia_id', $request->input('provincia_id'))
                    ->ignore($ciudadId),
            ],
            'activa' => ['nullable', 'boolean'],
        ]);

        $validated['activa'] = $request->boolean('activa');

        return $validated;
    }
}
