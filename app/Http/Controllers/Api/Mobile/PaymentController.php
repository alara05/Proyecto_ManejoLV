<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Boleto;
use App\Models\Pago;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    use MobilePayloads;

    public function store(Request $request, Boleto $boleto): JsonResponse
    {
        abort_unless($boleto->user_id === $request->user()->id || $boleto->cliente_email === $request->user()->email, 403);

        $validated = $request->validate([
            'metodo' => ['required', 'in:tarjeta,transferencia,deposito'],
            'referencia' => ['nullable', 'string', 'max:120'],
            'titular' => ['nullable', 'string', 'max:120'],
            'banco' => ['nullable', 'string', 'max:120'],
        ]);

        Pago::updateOrCreate(
            ['boleto_id' => $boleto->id],
            [
                'metodo' => $validated['metodo'],
                'monto' => $boleto->precio,
                'estado' => 'pendiente',
                'observacion' => collect([
                    'Pago enviado desde app movil',
                    isset($validated['referencia']) ? 'Referencia: '.$validated['referencia'] : null,
                    isset($validated['titular']) ? 'Titular: '.$validated['titular'] : null,
                    isset($validated['banco']) ? 'Banco: '.$validated['banco'] : null,
                ])->filter()->implode(' | '),
            ]
        );

        $boleto->forceFill(['estado' => 'pendiente'])->save();

        return response()->json(['data' => $this->ticketPayload($boleto->refresh())]);
    }
}
