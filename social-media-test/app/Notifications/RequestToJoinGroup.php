<?php

namespace App\Notifications;

use App\Models\Group;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RequestToJoinGroup extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     * 
     * @param Group $group El grupo al que se quiere unir
     * @param User $user El usuario que solicita unirse
     */
    public function __construct(
        public Group $group,
        public User $user
    ) {
   
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param object $notifiable El objeto notificable (normalmente User)
     * @return array<int, string> Canales de notificación
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param object $notifiable El objeto notificable
     * @return MailMessage El mensaje de correo configurado
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('User "'.$this->user->name.'" requested to join to group "'.$this->group->name.'"')
            ->action('Approve Request', url(route('group.profile', $this->group)))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the database representation of the notification.
     *
     * @param object $notifiable El objeto notificable
     * @return array<string, mixed> Datos para almacenar en BD
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'group_id' => $this->group->id,
            'group_name' => $this->group->name,
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'message' => 'User "'.$this->user->name.'" requested to join your group "'.$this->group->name.'"',
            'action_url' => route('group.profile', $this->group),
            'type' => 'group_join_request', 
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param object $notifiable El objeto notificable
     * @return array<string, mixed> Datos para serialización
     */
    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable); // Reutilizamos los mismos datos
    }
}