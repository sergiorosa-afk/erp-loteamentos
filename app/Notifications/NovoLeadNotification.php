<?php

namespace App\Notifications;

use App\Models\Pessoa;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NovoLeadNotification extends Notification
{
    public function __construct(public readonly Pessoa $pessoa) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Novo Lead via Site: ' . $this->pessoa->nome)
            ->greeting('Novo lead recebido!')
            ->line('**Nome:** ' . $this->pessoa->nome)
            ->line('**Email:** ' . ($this->pessoa->email ?? '—'))
            ->line('**Celular:** ' . ($this->pessoa->celular ?? '—'))
            ->action('Ver Lead no ERP', route('pessoas.show', $this->pessoa))
            ->line('Lead registrado automaticamente via webhook do site.');
    }
}
