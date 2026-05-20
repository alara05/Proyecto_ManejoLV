<?php

namespace App\Notifications;

use App\Models\Boleto;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BoletoEmitidoNotification extends Notification
{
    public function __construct(private readonly Boleto $boleto) {}

    public function via(object $notifiable): array
    {
        return $notifiable instanceof AnonymousNotifiable ? ['mail'] : ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Boleto emitido - '.$this->boleto->codigo)
            ->greeting('Boleto emitido')
            ->line('Tu boleto esta listo para ser descargado.')
            ->line('Codigo: '.$this->boleto->codigo)
            ->action('Descargar boleto', route('cliente.boletos.pdf', $this->boleto));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'boleto_id' => $this->boleto->id,
            'codigo' => $this->boleto->codigo,
            'estado' => $this->boleto->estado,
            'mensaje' => 'Boleto emitido y disponible para descarga.',
            'url' => route('cliente.boletos.pdf', $this->boleto),
        ];
    }
}
