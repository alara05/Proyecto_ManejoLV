<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use App\Models\Frecuencia;
use App\Models\Salida;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SalidaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $salidas = Salida::with([
            'frecuencia.cooperativa',
            'frecuencia.origen.provincia',
            'frecuencia.destino.provincia',
            'bus.cooperativa',
        ])
            ->orderByDesc('fecha')
            ->orderByDesc('hora_salida')
            ->paginate(10);

        return view('salidas.index', compact('salidas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('salidas.create', $this->formData());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateSalida($request);
        $frecuencia = Frecuencia::where('activa', true)->findOrFail($validated['frecuencia_id']);

        $salida = Salida::create([
            'frecuencia_id' => $frecuencia->id,
            'bus_id' => $validated['bus_id'],
            'fecha' => $validated['fecha'],
            'hora_salida' => $frecuencia->hora_salida,
            'estado' => 'programada',
            'precio_base' => $validated['precio_base'],
            'generada_automaticamente' => false,
        ]);

        return redirect()
            ->route('salidas.show', $salida)
            ->with('success', 'Salida generada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Salida $salida): View
    {
        $salida->load([
            'frecuencia.cooperativa',
            'frecuencia.origen.provincia',
            'frecuencia.destino.provincia',
            'frecuencia.paradas.ciudad.provincia',
            'bus.cooperativa',
        ]);

        return view('salidas.show', compact('salida'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Salida $salida): RedirectResponse
    {
        $salida->delete();

        return redirect()
            ->route('salidas.index')
            ->with('success', 'Salida eliminada correctamente.');
    }

    private function formData(): array
    {
        return [
            'frecuencias' => Frecuencia::where('activa', true)
                ->with(['cooperativa', 'origen.provincia', 'destino.provincia'])
                ->orderBy('hora_salida')
                ->get(),
            'buses' => Bus::where('activo', true)
                ->with('cooperativa')
                ->orderBy('numero')
                ->get(),
        ];
    }

    private function validateSalida(Request $request): array
    {
        return $request->validate([
            'frecuencia_id' => [
                'required',
                Rule::exists('frecuencias', 'id')->where('activa', true),
            ],
            'bus_id' => [
                'required',
                Rule::exists('buses', 'id')->where('activo', true),
            ],
            'fecha' => ['required', 'date', 'after_or_equal:today'],
            'precio_base' => ['required', 'numeric', 'min:0', 'max:999999.99'],
        ]);
    }
}
