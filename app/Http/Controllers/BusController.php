<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use App\Models\Cooperativa;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $buses = Bus::with('cooperativa')
            ->latest()
            ->paginate(10);

        return view('buses.index', compact('buses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $cooperativas = Cooperativa::where('activa', true)
            ->orderBy('nombre')
            ->get();

        return view('buses.create', compact('cooperativas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        Bus::create($this->validateBus($request));

        return redirect()
            ->route('buses.index')
            ->with('success', 'Bus registrado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Bus $bus): View
    {
        $bus->load('cooperativa');

        return view('buses.show', compact('bus'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bus $bus): View
    {
        $cooperativas = Cooperativa::where('activa', true)
            ->orderBy('nombre')
            ->get();

        return view('buses.edit', compact('bus', 'cooperativas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bus $bus): RedirectResponse
    {
        $bus->update($this->validateBus($request, $bus));

        return redirect()
            ->route('buses.index')
            ->with('success', 'Bus actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bus $bus): RedirectResponse
    {
        $bus->delete();

        return redirect()
            ->route('buses.index')
            ->with('success', 'Bus eliminado correctamente.');
    }

    private function validateBus(Request $request, ?Bus $bus = null): array
    {
        $busId = $bus?->id;

        $validated = $request->validate([
            'cooperativa_id' => ['required', 'exists:cooperativas,id'],
            'numero' => [
                'required',
                'string',
                'max:255',
                Rule::unique('buses', 'numero')
                    ->where('cooperativa_id', $request->input('cooperativa_id'))
                    ->ignore($busId),
            ],
            'placa' => [
                'required',
                'string',
                'max:10',
                Rule::unique('buses', 'placa')->ignore($busId),
            ],
            'marca_chasis' => ['nullable', 'string', 'max:255'],
            'marca_carroceria' => ['nullable', 'string', 'max:255'],
            'anio' => ['nullable', 'integer', 'min:1980', 'max:' . ((int) date('Y') + 1)],
            'capacidad_total' => ['required', 'integer', 'min:1', 'max:100'],
            'foto_path' => ['nullable', 'string', 'max:255'],
            'activo' => ['nullable', 'boolean'],
        ]);

        $validated['placa'] = strtoupper($validated['placa']);
        $validated['activo'] = $request->boolean('activo');

        return $validated;
    }
}
