<?php

namespace App\Http\Controllers;

use App\Models\Boleto;
use Illuminate\View\View;

class ClienteHistorialBoletoController extends Controller
{
    public function __invoke(): View
    {
        $boletos = Boleto::with([
            'salida.frecuencia.origen',
            'salida.frecuencia.destino',
            'salida.bus',
            'asiento.tipoAsiento',
            'pago',
        ])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('cliente.boletos.historial', compact('boletos'));
    }
}
