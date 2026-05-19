<?php

namespace App\Http\Controllers;

use App\Models\Boleto;
use App\Models\Pago;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PagoController extends Controller
{
    public function index(): View
    {
        $pagos = Pago::with([
            'boleto.salida.frecuencia.origen',
            'boleto.salida.frecuencia.destino',
            'boleto.asiento',
            'validador',
        ])
            ->latest()
            ->paginate(10);

        return view('pagos.index', compact('pagos'));
    }

    public function create(Boleto $boleto): View
    {
        $boleto->load([
            'pago',
            'salida.frecuencia.origen.provincia',
            'salida.frecuencia.destino.provincia',
            'asiento.tipoAsiento',
        ]);

        return view('cliente.pagos.create', [
            'boleto' => $boleto,
            'metodos' => $this->metodosPago(),
        ]);
    }

    public function store(Request $request, Boleto $boleto): RedirectResponse
    {
        $validated = $request->validate([
            'metodo' => ['required', 'in:transferencia,deposito'],
            'monto' => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
            'comprobante' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
            'observacion' => ['nullable', 'string', 'max:1000'],
        ]);

        $boleto->load('pago');

        if ($boleto->pago && $boleto->pago->estado === 'validado') {
            return redirect()
                ->route('cliente.boletos.show', $boleto)
                ->with('success', 'Este boleto ya tiene un pago validado.');
        }

        $path = $request->file('comprobante')->store('comprobantes', 'public');

        Pago::updateOrCreate(
            ['boleto_id' => $boleto->id],
            [
                'validado_por' => null,
                'metodo' => $validated['metodo'],
                'monto' => $validated['monto'],
                'comprobante_path' => $path,
                'estado' => 'pendiente',
                'validado_at' => null,
                'observacion' => $validated['observacion'] ?? null,
            ]
        );

        return redirect()
            ->route('cliente.pagos.create', $boleto)
            ->with('success', 'Comprobante cargado correctamente. El pago queda pendiente de validacion.');
    }

    public function show(Pago $pago): View
    {
        $pago->load([
            'boleto.salida.frecuencia.origen.provincia',
            'boleto.salida.frecuencia.destino.provincia',
            'boleto.salida.bus',
            'boleto.asiento.tipoAsiento',
            'validador',
        ]);

        return view('pagos.show', compact('pago'));
    }

    public function validar(Pago $pago): RedirectResponse
    {
        $pago->load('boleto');

        $pago->update([
            'estado' => 'validado',
            'validado_por' => auth()->id(),
            'validado_at' => now(),
        ]);

        $pago->boleto->update([
            'estado' => 'pagado',
            'vendido_at' => now(),
        ]);

        return redirect()
            ->route('pagos.show', $pago)
            ->with('success', 'Pago validado correctamente.');
    }

    public function rechazar(Request $request, Pago $pago): RedirectResponse
    {
        $validated = $request->validate([
            'observacion' => ['nullable', 'string', 'max:1000'],
        ]);

        $pago->update([
            'estado' => 'rechazado',
            'validado_por' => auth()->id(),
            'validado_at' => now(),
            'observacion' => $validated['observacion'] ?? $pago->observacion,
        ]);

        return redirect()
            ->route('pagos.show', $pago)
            ->with('success', 'Pago rechazado correctamente.');
    }

    private function metodosPago(): array
    {
        return [
            'transferencia' => 'Transferencia bancaria',
            'deposito' => 'Deposito bancario',
        ];
    }
}
