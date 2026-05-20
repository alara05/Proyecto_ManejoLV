<?php

namespace App\Http\Controllers;

use App\Models\Boleto;
use App\Models\RegistroAcceso;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AccesoPasajeroController extends Controller
{
    public function index(): View
    {
        $this->authorizeBusAccess();

        $registros = RegistroAcceso::with([
            'boleto.salida.frecuencia.origen',
            'boleto.salida.frecuencia.destino',
            'boleto.asiento',
            'registrador',
        ])
            ->latest('registrado_at')
            ->paginate(10);

        return view('accesos.index', compact('registros'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeBusAccess();

        $validated = $request->validate([
            'codigo' => ['required', 'string', 'max:255'],
        ]);

        $codigo = trim($validated['codigo']);

        $boleto = Boleto::with([
            'salida.frecuencia.origen',
            'salida.frecuencia.destino',
            'asiento',
        ])->where('codigo', $codigo)->first();

        if (! $boleto) {
            return redirect()
                ->route('accesos.index')
                ->with('error', 'No existe un boleto con el codigo ingresado.');
        }

        $resultado = 'rechazado';
        $observacion = 'El boleto no esta pagado o ya fue usado.';

        DB::transaction(function () use ($boleto, &$resultado, &$observacion): void {
            $boleto->refresh();

            if ($boleto->estado === 'pagado') {
                $resultado = 'permitido';
                $observacion = 'Acceso permitido. Boleto marcado como usado.';

                $boleto->update([
                    'estado' => 'usado',
                ]);
            }

            RegistroAcceso::create([
                'boleto_id' => $boleto->id,
                'registrado_por' => auth()->id(),
                'registrado_at' => now(),
                'resultado' => $resultado,
                'observacion' => $observacion,
            ]);
        });

        return redirect()
            ->route('accesos.index')
            ->with($resultado === 'permitido' ? 'success' : 'error', $observacion);
    }

    private function authorizeBusAccess(): void
    {
        abort_unless(in_array(auth()->user()?->role, ['admin', 'personal_bus'], true), 403);
    }
}
