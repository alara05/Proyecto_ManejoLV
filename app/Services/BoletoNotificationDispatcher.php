<?php

namespace App\Services;

use App\Models\Boleto;
use App\Models\Pago;
use App\Notifications\BoletoEmitidoNotification;
use App\Notifications\CompraRegistradaNotification;
use App\Notifications\PagoPendienteNotification;
use App\Notifications\PagoValidadoNotification;
use Illuminate\Notifications\Notification as BaseNotification;
use Illuminate\Support\Facades\Notification;

class BoletoNotificationDispatcher
{
    public function compraRegistrada(Boleto $boleto): void
    {
        $this->notify($boleto, new CompraRegistradaNotification($boleto));
    }

    public function pagoPendiente(Pago $pago): void
    {
        $pago->loadMissing('boleto');

        $this->notify($pago->boleto, new PagoPendienteNotification($pago));
    }

    public function pagoValidado(Pago $pago): void
    {
        $pago->loadMissing('boleto');

        $this->notify($pago->boleto, new PagoValidadoNotification($pago));
    }

    public function boletoEmitido(Boleto $boleto): void
    {
        $this->notify($boleto, new BoletoEmitidoNotification($boleto));
    }

    private function notify(Boleto $boleto, BaseNotification $notification): void
    {
        $boleto->loadMissing('cliente');

        if ($boleto->cliente) {
            $boleto->cliente->notify($notification);
        }

        $clienteEmail = $boleto->cliente_email;
        $userEmail = $boleto->cliente?->email;

        if ($clienteEmail && $clienteEmail !== $userEmail) {
            Notification::route('mail', $clienteEmail)->notify($notification);
        }
    }
}
