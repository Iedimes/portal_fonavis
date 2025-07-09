<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class CustomResetPassword extends ResetPassword
{
    public function toMail($notifiable)
    {
        $resetUrl = $this->resetUrl($notifiable);

        return (new MailMessage)
            ->subject('Restablece tu contraseña')
            ->greeting('Hola, ' . $notifiable->name)
            ->line('Recibimos una solicitud para restablecer tu contraseña.')
            ->action('Restablecer contraseña', $resetUrl)
            ->line('Este enlace expirará en 60 minutos.')
            ->line('Si no hiciste esta solicitud, puedes ignorar este mensaje.')
            ->line('Si el botón anterior no funciona, copia y pega el siguiente enlace en tu navegador:')
            ->line($resetUrl)
            ->salutation("Saludos,\nEquipo del SIPP");
    }
}
