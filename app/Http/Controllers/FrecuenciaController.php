<?php

namespace App\Http\Controllers;

use App\Models\Ciudad;
use App\Models\Cooperativa;
use App\Models\Frecuencia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FrecuenciaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $frecuencias = Frecuencia::with(['cooperativa', 'origen.provincia', 'destino.provincia', 'paradas.ciudad.provincia'])
            ->latest()
            ->paginate(10);

        return view('frecuencias.index', compact('frecuencias'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('frecuencias.create', $this->formData());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateFrecuencia($request);

        DB::transaction(function () use ($validated) {
            $frecuencia = Frecuencia::create($validated['frecuencia']);
            $this->saveParadas($frecuencia, $validated['paradas']);
        });

        return redirect()
            ->route('frecuencias.index')
            ->with('success', 'Frecuencia ANT registrada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Frecuencia $frecuencia): View
    {
        $frecuencia->load(['cooperativa', 'origen.provincia', 'destino.provincia', 'paradas.ciudad.provincia']);

        return view('frecuencias.show', compact('frecuencia'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Frecuencia $frecuencia): View
    {
        $frecuencia->load('paradas');

        return view('frecuencias.edit', ['frecuencia' => $frecuencia] + $this->formData());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Frecuencia $frecuencia): RedirectResponse
    {
        $validated = $this->validateFrecuencia($request, $frecuencia);

        DB::transaction(function () use ($frecuencia, $validated) {
            $frecuencia->update($validated['frecuencia']);
            $this->saveParadas($frecuencia, $validated['paradas']);
        });

        return redirect()
            ->route('frecuencias.index')
            ->with('success', 'Frecuencia ANT actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Frecuencia $frecuencia): RedirectResponse
    {
        $frecuencia->delete();

        return redirect()
            ->route('frecuencias.index')
            ->with('success', 'Frecuencia ANT eliminada correctamente.');
    }

    private function formData(): array
    {
        return [
            'cooperativas' => Cooperativa::where('activa', true)->orderBy('nombre')->get(),
            'ciudades' => Ciudad::where('activa', true)->with('provincia')->orderBy('nombre')->get(),
        ];
    }

    private function validateFrecuencia(Request $request, ?Frecuencia $frecuencia = null): array
    {
        $frecuenciaId = $frecuencia?->id;

        $validated = $request->validate([
            'cooperativa_id' => ['required', 'exists:cooperativas,id'],
            'ciudad_origen_id' => ['required', 'exists:ciudades,id', 'different:ciudad_destino_id'],
            'ciudad_destino_id' => ['required', 'exists:ciudades,id'],
            'hora_salida' => ['required', 'date_format:H:i'],
            'numero_resolucion_ant' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('frecuencias', 'numero_resolucion_ant')->ignore($frecuenciaId),
            ],
            'fecha_resolucion_ant' => ['nullable', 'date'],
            'activa' => ['nullable', 'boolean'],
            'paradas' => ['nullable', 'array'],
            'paradas.*.ciudad_id' => ['nullable', 'exists:ciudades,id'],
            'paradas.*.minutos_desde_origen' => ['nullable', 'integer', 'min:1', 'max:1440'],
        ]);

        $paradas = $this->normalizeParadas($validated['paradas'] ?? [], $validated['ciudad_origen_id'], $validated['ciudad_destino_id']);

        return [
            'frecuencia' => [
                'cooperativa_id' => $validated['cooperativa_id'],
                'ciudad_origen_id' => $validated['ciudad_origen_id'],
                'ciudad_destino_id' => $validated['ciudad_destino_id'],
                'hora_salida' => $validated['hora_salida'],
                'numero_resolucion_ant' => $validated['numero_resolucion_ant'] ?? null,
                'fecha_resolucion_ant' => $validated['fecha_resolucion_ant'] ?? null,
                'tiene_paradas' => count($paradas) > 0,
                'activa' => $request->boolean('activa'),
            ],
            'paradas' => $paradas,
        ];
    }

    private function normalizeParadas(array $paradas, int|string $origenId, int|string $destinoId): array
    {
        $normalized = [];
        $ciudadesUsadas = [];

        foreach ($paradas as $parada) {
            if (empty($parada['ciudad_id'])) {
                continue;
            }

            $ciudadId = (int) $parada['ciudad_id'];

            if ($ciudadId === (int) $origenId || $ciudadId === (int) $destinoId || in_array($ciudadId, $ciudadesUsadas, true)) {
                continue;
            }

            $ciudadesUsadas[] = $ciudadId;
            $normalized[] = [
                'ciudad_id' => $ciudadId,
                'minutos_desde_origen' => (int) ($parada['minutos_desde_origen'] ?? 1),
            ];
        }

        return $normalized;
    }

    private function saveParadas(Frecuencia $frecuencia, array $paradas): void
    {
        $frecuencia->paradas()->delete();

        foreach ($paradas as $index => $parada) {
            $frecuencia->paradas()->create([
                'ciudad_id' => $parada['ciudad_id'],
                'orden' => $index + 1,
                'minutos_desde_origen' => $parada['minutos_desde_origen'],
            ]);
        }
    }
}
