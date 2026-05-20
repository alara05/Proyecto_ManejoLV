<?php

namespace App\Notifications;

use App\Models\Pago;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PagoRechazadoNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly Pago $pago)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'tipo' => 'pago_rechazado',
            'titulo' => 'Pago rechazado',
            'mensaje' => 'El pago del boleto ' . $this->pago->boleto->codigo . ' fue rechazado. Revisa la observacion y carga un nuevo comprobante.',
            'boleto_id' => $this->pago->boleto_id,
            'pago_id' => $this->pago->id,
            'url' => route('cliente.pagos.create', $this->pago->boleto),
        ];
    }
}
