<?php

namespace App\Http\Controllers;

use App\Models\Boleto;
use App\Services\BoletoPdfGenerator;
use Illuminate\Http\Response;

class BoletoPdfController extends Controller
{
    public function __invoke(Boleto $boleto, BoletoPdfGenerator $generator): Response
    {
        $boleto->load([
            'salida.frecuencia.origen.provincia',
            'salida.frecuencia.destino.provincia',
            'salida.bus.cooperativa',
            'asiento.tipoAsiento',
        ]);

        return response($generator->generate($boleto), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="boleto-' . $boleto->codigo . '.pdf"',
        ]);
    }
}
