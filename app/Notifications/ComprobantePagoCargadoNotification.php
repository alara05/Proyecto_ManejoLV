<?php

namespace App\Notifications;

use App\Models\Pago;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ComprobantePagoCargadoNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly Pago $pago)
    {
    }

    public function via(object $notifiable): array
    {
        if ($notifiable instanceof AnonymousNotifiable) {
            return ['mail'];
        }

        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Comprobante de pago pendiente')
            ->greeting('Nuevo comprobante pendiente')
            ->line('Se cargo un comprobante para el boleto ' . $this->pago->boleto->codigo . '.')
            ->line('Monto reportado: $' . number_format((float) $this->pago->monto, 2))
            ->action('Revisar pago', route('pagos.show', $this->pago))
            ->line('Ingresa como administrador u oficinista para validar o rechazar el pago.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'tipo' => 'comprobante_cargado',
            'titulo' => 'Comprobante pendiente',
            'mensaje' => 'Se cargo un comprobante para el boleto ' . $this->pago->boleto->codigo . '. Revisa y valida el pago.',
            'boleto_id' => $this->pago->boleto_id,
            'pago_id' => $this->pago->id,
            'url' => route('pagos.show', $this->pago),
        ];
    }
}
