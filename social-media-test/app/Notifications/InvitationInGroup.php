<?php

namespace App\Notifications;

use App\Models\Group;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvitationInGroup extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Group $group, public int $hours, public string $token)
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
            ->subject('Invitación a grupo: ' . $this->group->name)
            ->line('Has sido invitado a unirte al grupo "' . $this->group->name . '"')
            ->action('Unirse al Grupo', url(route('group.approveInvitation', $this->token)))
            ->line('El enlace será válido por las próximas ' . $this->hours . ' horas');
    }

    /**
     * Get the array representation for database storage.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'group_invitation',
            'message' => 'Invitación al grupo: ' . $this->group->name,
            'group_id' => $this->group->id,
            'group_name' => $this->group->name,
            'group_avatar' => $this->group->avatar_url,
            'hours_valid' => $this->hours,
            'token' => $this->token,
            'invitation_url' => route('group.approveInvitation', $this->token),
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