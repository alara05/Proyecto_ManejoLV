<?php

namespace App\Notifications;

use App\Models\Pago;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PagoValidadoNotification extends Notification
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
            ->subject('Pago validado - '.$this->pago->boleto->codigo)
            ->greeting('Pago validado')
            ->line('Tu pago fue validado correctamente.')
            ->line('El boleto ya se encuentra en estado pagado.')
            ->action('Ver boleto', route('cliente.boletos.show', $this->pago->boleto));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'boleto_id' => $this->pago->boleto_id,
            'pago_id' => $this->pago->id,
            'codigo' => $this->pago->boleto->codigo,
            'estado' => $this->pago->estado,
            'mensaje' => 'Pago validado correctamente.',
        ];
    }
}
