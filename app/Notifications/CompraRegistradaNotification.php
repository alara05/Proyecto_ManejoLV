<?php

namespace App\Notifications;

use App\Models\Boleto;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CompraRegistradaNotification extends Notification
{
    public function __construct(private readonly Boleto $boleto) {}

    public function via(object $notifiable): array
    {
        return $notifiable instanceof AnonymousNotifiable ? ['mail'] : ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Compra registrada - '.$this->boleto->codigo)
            ->greeting('Compra registrada')
            ->line('Tu boleto fue reservado correctamente.')
            ->line('Codigo: '.$this->boleto->codigo)
            ->line('Pasajero: '.$this->boleto->pasajero_nombre)
            ->line('Total: $'.number_format((float) $this->boleto->precio, 2))
            ->action('Ver boleto', route('cliente.boletos.show', $this->boleto));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'boleto_id' => $this->boleto->id,
            'codigo' => $this->boleto->codigo,
            'estado' => $this->boleto->estado,
            'mensaje' => 'Compra registrada correctamente.',
        ];
    }
}
