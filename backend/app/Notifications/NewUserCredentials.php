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
                    ->subject('Bienvenido al Portal DX License Manager')
                    ->greeting('Hola ' . $notifiable->name . '!')
                    ->line('Se ha creado tu cuenta de acceso al portal corporativo DX License Manager.')
                    ->line('A continuación encontrarás tus credenciales de acceso iniciales:')
                    ->line('**Email:** ' . $notifiable->email)
                    ->line('**Contraseña:** ' . $this->password)
                    ->action('Acceder al Portal', url('/login'))
                    ->line('Por seguridad, te recomendamos cambiar tu contraseña una vez hayas iniciado sesión desde la sección de "Mi Perfil".')
                    ->line('Gracias por formar parte del equipo.');
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
