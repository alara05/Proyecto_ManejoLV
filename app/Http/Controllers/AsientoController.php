<?php

namespace App\Http\Controllers;

use App\Models\Asiento;
use App\Models\Bus;
use App\Models\TipoAsiento;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Illuminate\View\View;

class AsientoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $asientos = Asiento::with(['bus.cooperativa', 'tipoAsiento.cooperativa'])
            ->latest()
            ->paginate(10);

        return view('asientos.index', compact('asientos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('asientos.create', $this->formOptions());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        Asiento::create($this->validateAsiento($request));

        return redirect()
            ->route('asientos.index')
            ->with('success', 'Asiento registrado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Asiento $asiento): View
    {
        $asiento->load(['bus.cooperativa', 'tipoAsiento.cooperativa']);

        return view('asientos.show', compact('asiento'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Asiento $asiento): View
    {
        return view('asientos.edit', [
            'asiento' => $asiento,
            ...$this->formOptions(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Asiento $asiento): RedirectResponse
    {
        $asiento->update($this->validateAsiento($request, $asiento));

        return redirect()
            ->route('asientos.index')
            ->with('success', 'Asiento actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Asiento $asiento): RedirectResponse
    {
        if ($asiento->boletos()->exists()) {
            return redirect()
                ->route('asientos.index')
                ->with('error', 'No se puede eliminar el asiento porque tiene boletos asociados.');
        }

        $asiento->delete();

        return redirect()
            ->route('asientos.index')
            ->with('success', 'Asiento eliminado correctamente.');
    }

    private function validateAsiento(Request $request, ?Asiento $asiento = null): array
    {
        $asientoId = $asiento?->id;

        $validator = validator($request->all(), [
            'bus_id' => ['required', 'exists:buses,id'],
            'tipo_asiento_id' => ['required', 'exists:tipo_asientos,id'],
            'numero' => [
                'required',
                'string',
                'max:20',
                Rule::unique('asientos', 'numero')
                    ->where('bus_id', $request->input('bus_id'))
                    ->ignore($asientoId),
            ],
            'activo' => ['nullable', 'boolean'],
        ]);

        $validator->after(function (Validator $validator) use ($request): void {
            $bus = Bus::find($request->input('bus_id'));
            $tipoAsiento = TipoAsiento::find($request->input('tipo_asiento_id'));

            if (! $bus || ! $tipoAsiento) {
                return;
            }

            if (
                $tipoAsiento->cooperativa_id !== null
                && (int) $tipoAsiento->cooperativa_id !== (int) $bus->cooperativa_id
            ) {
                $validator->errors()->add(
                    'tipo_asiento_id',
                    'El tipo de asiento debe pertenecer a la misma cooperativa del bus.'
                );
            }
        });

        $validated = $validator->validate();
        $validated['numero'] = strtoupper($validated['numero']);
        $validated['activo'] = $request->boolean('activo');

        return $validated;
    }

    private function formOptions(): array
    {
        return [
            'buses' => Bus::with('cooperativa')
                ->where('activo', true)
                ->orderBy('numero')
                ->get(),
            'tipoAsientos' => TipoAsiento::with('cooperativa')
                ->where('activo', true)
                ->orderBy('nombre')
                ->get(),
        ];
    }
}
