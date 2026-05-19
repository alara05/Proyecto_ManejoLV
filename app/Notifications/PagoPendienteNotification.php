<?php

namespace App\Notifications;

use App\Models\Pago;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PagoPendienteNotification extends Notification
{
    public function __construct(private readonly Pago $pago)
    {
        $this->pago->loadMissing('boleto');
    }

    public function via(object $notifiable): array
    {
        return $notifiable instanceof AnonymousNotifiable ? ['mail'] : ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Pago pendiente de validacion - '.$this->pago->boleto->codigo)
            ->greeting('Pago recibido')
            ->line('Tu comprobante fue cargado y queda pendiente de validacion manual.')
            ->line('Metodo: '.ucfirst($this->pago->metodo))
            ->line('Monto: $'.number_format((float) $this->pago->monto, 2))
            ->action('Ver estado del pago', route('cliente.pagos.create', $this->pago->boleto));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'boleto_id' => $this->pago->boleto_id,
            'pago_id' => $this->pago->id,
            'codigo' => $this->pago->boleto->codigo,
            'estado' => $this->pago->estado,
            'mensaje' => 'Pago pendiente de validacion.',
        ];
    }
}
