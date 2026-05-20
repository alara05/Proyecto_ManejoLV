<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Models\Asiento;
use App\Models\Boleto;
use App\Models\Salida;
use Illuminate\Support\Str;

trait MobilePayloads
{
    private function travelPayload(Salida $salida): array
    {
        $salida->loadMissing([
            'frecuencia.origen',
            'frecuencia.destino',
            'bus.cooperativa',
            'bus.asientos.tipoAsiento',
            'boletos',
        ]);

        $ocupados = $salida->boletos->pluck('asiento_id')->all();

        return [
            'id' => $salida->id,
            'origin' => $salida->frecuencia->origen->nombre,
            'destination' => $salida->frecuencia->destino->nombre,
            'date' => $salida->fecha->format('Y-m-d'),
            'date_label' => $salida->fecha->format('d/m/Y'),
            'time' => Str::of($salida->hora_salida)->substr(0, 5)->toString(),
            'price' => (float) $salida->precio_base,
            'status' => $salida->estado,
            'cooperative' => $salida->bus->cooperativa->nombre ?? 'Cuchao',
            'bus' => [
                'id' => $salida->bus->id,
                'number' => $salida->bus->numero,
                'plate' => $salida->bus->placa,
            ],
            'seats' => $salida->bus->asientos
                ->where('activo', true)
                ->sortBy(fn (Asiento $asiento) => (int) preg_replace('/\D+/', '', $asiento->numero))
                ->values()
                ->map(fn (Asiento $asiento): array => [
                    'id' => $asiento->id,
                    'number' => $asiento->numero,
                    'type' => $asiento->tipoAsiento->nombre ?? 'General',
                    'price' => round((float) $salida->precio_base + (float) ($asiento->tipoAsiento?->recargo ?? 0), 2),
                    'occupied' => in_array($asiento->id, $ocupados, true),
                ])
                ->all(),
        ];
    }

    private function ticketPayload(Boleto $boleto): array
    {
        $boleto->loadMissing([
            'salida.frecuencia.origen',
            'salida.frecuencia.destino',
            'salida.bus',
            'asiento',
            'pago',
        ]);

        return [
            'id' => $boleto->id,
            'code' => $boleto->codigo,
            'passenger_name' => $boleto->pasajero_nombre,
            'passenger_id' => $boleto->pasajero_cedula,
            'origin' => $boleto->origen?->nombre ?? $boleto->salida->frecuencia->origen->nombre,
            'destination' => $boleto->destino?->nombre ?? $boleto->salida->frecuencia->destino->nombre,
            'date' => $boleto->salida->fecha->format('Y-m-d'),
            'date_label' => $boleto->salida->fecha->format('d/m/Y'),
            'time' => Str::of($boleto->salida->hora_salida)->substr(0, 5)->toString(),
            'seat' => $boleto->asiento?->numero,
            'price' => (float) $boleto->precio,
            'status' => $boleto->estado,
            'payment' => $boleto->pago ? [
                'method' => $boleto->pago->metodo,
                'amount' => (float) $boleto->pago->monto,
                'status' => $boleto->pago->estado,
                'receipt_url' => $boleto->pago->comprobante_path ? asset('storage/'.$boleto->pago->comprobante_path) : null,
            ] : null,
            'qr_value' => route('cliente.boletos.show', $boleto),
            'pdf_url' => route('cliente.boletos.pdf', $boleto),
        ];
    }
}
