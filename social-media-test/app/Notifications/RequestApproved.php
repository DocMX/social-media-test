<?php

namespace App\Notifications;

use App\Models\Group;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RequestApproved extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Group $group,
        public User $user,
        public bool $approved,
        public User $processedBy // Quién aprobó/rechazó la solicitud
    ) {
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
        $action = $this->approved ? 'approved' : 'rejected';
        $actionText = $this->approved ? 'aprobada' : 'rechazada';

        return (new MailMessage)
            ->subject('Solicitud ' . $actionText)
            ->line('Tu solicitud para unirte al grupo "' . $this->group->name . '" ha sido ' . $actionText)
            ->line('Procesado por: ' . $this->processedBy->name)
            ->action('Ver Grupo', url(route('group.profile', $this->group)))
            ->line('Gracias por usar nuestra aplicación!');
    }

    /**
     * Get the array representation for database storage.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $action = $this->approved ? 'approved' : 'rejected';
        $actionText = $this->approved ? 'aprobó' : 'rechazó';

        return [
            'type' => 'group_request_' . $action,
            'message' => $this->processedBy->name . ' ' . $actionText . ' tu solicitud al grupo',
            'group_id' => $this->group->id,
            'group_name' => $this->group->name,
            'group_avatar' => $this->group->avatar_url,
            'processed_by_id' => $this->processedBy->id,
            'processed_by_name' => $this->processedBy->name,
            'processed_by_username' => $this->processedBy->username,
            'decision' => $action,
            'group_url' => route('group.profile', $this->group),
            'processed_at' => now()->toDateTimeString(),
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