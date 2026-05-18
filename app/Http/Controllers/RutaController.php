<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use App\Models\Ciudad;
use App\Models\Cooperativa;
use App\Models\Ruta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RutaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $rutas = Ruta::with(['cooperativa', 'bus', 'origen', 'destino'])
            ->latest()
            ->paginate(10);

        return view('rutas.index', compact('rutas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('rutas.create', $this->formData());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        Ruta::create($this->validateRuta($request));

        return redirect()
            ->route('rutas.index')
            ->with('success', 'Ruta registrada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Ruta $ruta): View
    {
        $ruta->load(['cooperativa', 'bus', 'origen', 'destino']);

        return view('rutas.show', compact('ruta'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ruta $ruta): View
    {
        return view('rutas.edit', ['ruta' => $ruta] + $this->formData());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ruta $ruta): RedirectResponse
    {
        $ruta->update($this->validateRuta($request, $ruta));

        return redirect()
            ->route('rutas.index')
            ->with('success', 'Ruta actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ruta $ruta): RedirectResponse
    {
        $ruta->delete();

        return redirect()
            ->route('rutas.index')
            ->with('success', 'Ruta eliminada correctamente.');
    }

    private function formData(): array
    {
        return [
            'cooperativas' => Cooperativa::where('activa', true)->orderBy('nombre')->get(),
            'buses' => Bus::where('activo', true)->with('cooperativa')->orderBy('numero')->get(),
            'ciudades' => Ciudad::where('activa', true)->orderBy('nombre')->get(),
        ];
    }

    private function validateRuta(Request $request, ?Ruta $ruta = null): array
    {
        $rutaId = $ruta?->id;

        $validated = $request->validate([
            'cooperativa_id' => ['required', 'exists:cooperativas,id'],
            'bus_id' => ['nullable', 'exists:buses,id'],
            'ciudad_origen_id' => ['required', 'exists:ciudades,id', 'different:ciudad_destino_id'],
            'ciudad_destino_id' => ['required', 'exists:ciudades,id'],
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('rutas', 'nombre')
                    ->where('cooperativa_id', $request->input('cooperativa_id'))
                    ->where('ciudad_origen_id', $request->input('ciudad_origen_id'))
                    ->where('ciudad_destino_id', $request->input('ciudad_destino_id'))
                    ->ignore($rutaId),
            ],
            'tipo_viaje' => ['required', Rule::in(['directo', 'con_paradas'])],
            'distancia_km' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'duracion_minutos' => ['nullable', 'integer', 'min:1', 'max:1440'],
            'activa' => ['nullable', 'boolean'],
        ]);

        $validated['bus_id'] = $validated['bus_id'] ?? null;
        $validated['activa'] = $request->boolean('activa');

        return $validated;
    }
}
