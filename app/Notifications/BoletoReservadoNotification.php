<?php

namespace App\Notifications;

use App\Models\Boleto;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BoletoReservadoNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly Boleto $boleto)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'tipo' => 'boleto_reservado',
            'titulo' => 'Boleto reservado',
            'mensaje' => 'Tu boleto ' . $this->boleto->codigo . ' fue reservado correctamente. Carga el comprobante para completar la compra.',
            'boleto_id' => $this->boleto->id,
            'url' => route('cliente.boletos.show', $this->boleto),
        ];
    }
}
