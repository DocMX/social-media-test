<?php

namespace App\Notifications;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReactionAddedOnComment extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Post $post, public Comment $comment, public User $user)
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
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('User "' . $this->user->username . '" has liked your comment. Your comment: ')
            ->line('"' . $this->comment->comment . '"')
            ->action('View Post', url(route('post.view', $this->post->id)))
            ->line('Thank you for using our application!');
    }



    public function toDatabase($notifiable): array
    {
        return [
            'type' => 'comment_reaction',
            'comment_excerpt' => str()->limit($this->comment->comment, 50),
            'post_id' => $this->post->id,
            'post_slug' => $this->post->slug ?? null,
            'user_id' => $this->user->id,
            'user_username' => $this->user->username,
            'user_name' => $this->user->name,
            'comment_author' => $this->comment->user->name,
        ];
    }
}
