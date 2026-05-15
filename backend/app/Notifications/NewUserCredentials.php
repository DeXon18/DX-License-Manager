<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewUserCredentials extends Notification
{
    use Queueable;

    protected $password;

    /**
     * Create a new notification instance.
     */
    public function __construct($password)
    {
        $this->password = $password;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('🔐 Tus credenciales de acceso — DX License Manager')
                    ->greeting('¡Hola, ' . $notifiable->name . '!')
                    ->line('Bienvenido al portal corporativo. Tu cuenta ha sido habilitada correctamente para gestionar el inventario de licencias.')
                    ->line('Estas son tus credenciales de acceso provisionales:')
                    ->line('— **Usuario:** ' . $notifiable->email)
                    ->line('— **Contraseña:** ' . $this->password)
                    ->action('Iniciar Sesión Ahora', url('/login'))
                    ->line('Por motivos de seguridad, es obligatorio cambiar esta contraseña tras tu primer acceso desde el panel de "Mi Perfil".')
                    ->salutation('Saludos, el equipo de Soporte AYS');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
