<?php

namespace App\Notifications;

use App\Models\Post;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Broadcasting\PrivateChannel;

class ReactionAddedOnPost extends Notification implements ShouldQueue, ShouldBroadcast
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Post $post, public User $user)
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
        return ['database', 'mail', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Nueva reacción en tu publicación')
            ->line('El usuario "'.$this->user->username.'" reaccionó a tu publicación.')
            ->action('Ver publicación', $this->getPostUrl())
            ->line('Gracias por usar nuestra aplicación!');
    }

    /**
     * Get the array representation for database storage.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'post_reaction',
            'message' => "{$this->user->username} reaccionó a tu publicación",
            'post_id' => $this->post->id,
            'post_title' => $this->post->title,
            'post_excerpt' => str($this->post->content)->limit(100),
            'post_slug' => $this->post->slug,
            'user_id' => $this->user->id,
            'user_username' => $this->user->username,
            'user_name' => $this->user->name,
            'user_avatar' => $this->user->avatar_url,
            'link' => $this->getPostUrl(),
            'created_at' => now()->toDateTimeString(),
        ];
    }

    /**
     * Get the broadcast representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'id' => $this->id,
            'type' => 'post_reaction',
            'data' => $this->toDatabase($notifiable),
            'read_at' => null,
            'created_at' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('user.'.$this->post->user_id);
    }

    /**
     * Get the broadcast channel name.
     */
    public function broadcastAs()
    {
        return 'notification.created';
    }

    /**
     * Get the URL for the post
     */
    protected function getPostUrl(): string
    {
        return route('post.view', [
            'post' => $this->post->slug ?? $this->post->id,
            'id' => $this->post->id
        ]);
    }
}