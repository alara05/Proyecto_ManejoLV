<?php

namespace App\Http\Controllers;

use App\Models\Cooperativa;
use App\Models\TipoAsiento;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TipoAsientoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $tipoAsientos = TipoAsiento::with('cooperativa')
            ->withCount('asientos')
            ->latest()
            ->paginate(10);

        return view('tipo-asientos.index', compact('tipoAsientos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $cooperativas = $this->cooperativasActivas();

        return view('tipo-asientos.create', compact('cooperativas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        TipoAsiento::create($this->validateTipoAsiento($request));

        return redirect()
            ->route('tipo-asientos.index')
            ->with('success', 'Tipo de asiento registrado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(TipoAsiento $tipoAsiento): View
    {
        $tipoAsiento->load('cooperativa')->loadCount('asientos');

        return view('tipo-asientos.show', compact('tipoAsiento'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TipoAsiento $tipoAsiento): View
    {
        $cooperativas = $this->cooperativasActivas();

        return view('tipo-asientos.edit', compact('tipoAsiento', 'cooperativas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TipoAsiento $tipoAsiento): RedirectResponse
    {
        $tipoAsiento->update($this->validateTipoAsiento($request, $tipoAsiento));

        return redirect()
            ->route('tipo-asientos.index')
            ->with('success', 'Tipo de asiento actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TipoAsiento $tipoAsiento): RedirectResponse
    {
        if ($tipoAsiento->asientos()->exists()) {
            return redirect()
                ->route('tipo-asientos.index')
                ->with('error', 'No se puede eliminar el tipo de asiento porque tiene asientos asociados.');
        }

        $tipoAsiento->delete();

        return redirect()
            ->route('tipo-asientos.index')
            ->with('success', 'Tipo de asiento eliminado correctamente.');
    }

    private function validateTipoAsiento(Request $request, ?TipoAsiento $tipoAsiento = null): array
    {
        $tipoAsientoId = $tipoAsiento?->id;

        $validated = $request->validate([
            'cooperativa_id' => ['nullable', 'exists:cooperativas,id'],
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tipo_asientos', 'nombre')
                    ->where('cooperativa_id', $request->input('cooperativa_id'))
                    ->ignore($tipoAsientoId),
            ],
            'descripcion' => ['nullable', 'string'],
            'recargo' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'activo' => ['nullable', 'boolean'],
        ]);

        $validated['cooperativa_id'] = $validated['cooperativa_id'] ?? null;
        $validated['descripcion'] = $validated['descripcion'] ?? null;
        $validated['activo'] = $request->boolean('activo');

        return $validated;
    }

    private function cooperativasActivas()
    {
        return Cooperativa::where('activa', true)
            ->orderBy('nombre')
            ->get();
    }
}
