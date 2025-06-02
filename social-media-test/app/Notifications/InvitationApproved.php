<?php

namespace App\Notifications;

use App\Models\Group;
use App\Models\GroupUser;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvitationApproved extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Group $group, public User $user)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Nuevo miembro en el grupo: ' . $this->group->name)
            ->line('El usuario "' . $this->user->name . '" se ha unido al grupo "' . $this->group->name . '"')
            ->action('Abrir Grupo', url(route('group.profile', $this->group)))
            ->line('¡Gracias por usar nuestra aplicación!');
    }

    /**
     * Get the array representation for database storage.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'group_invitation_approved',
            'message' => 'Nuevo miembro en el grupo: ' . $this->group->name,
            'group_id' => $this->group->id,
            'group_name' => $this->group->name,
            'group_avatar' => $this->group->avatar_url,
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'user_username' => $this->user->username,
            'user_avatar' => $this->user->avatar_url,
            'group_url' => route('group.profile', $this->group),
            'created_at' => now()->toDateTimeString(),
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}