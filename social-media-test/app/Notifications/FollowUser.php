<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FollowUser extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public User $user, public bool $follow = true)
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
        return ['database', 'mail']; // Agregamos 'database' a los canales
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        if ($this->follow) {
            $subject = 'User "' . $this->user->username . '" has followed you';
        } else {
            $subject = 'User "' . $this->user->username . '" is no more following you';
        }
        return ( new MailMessage )
            ->subject($subject)
            ->line($subject)
            ->action('View Profile', url(route('profile', $this->user)))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation for database storage.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => $this->follow ? 'user_followed' : 'user_unfollowed',
            'message' => $this->follow 
                ? "{$this->user->username} started following you"
                : "{$this->user->username} stopped following you",
            'user_id' => $this->user->id,
            'user_username' => $this->user->username,
            'user_avatar' => $this->user->avatar_url,
            'link' => route('profile', $this->user),
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable); // Puedes usar el mismo formato que toDatabase
    }
}