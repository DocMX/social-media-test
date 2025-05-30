<?php

namespace App\Notifications;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class CommentCreated extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Comment $comment,
        public Post $post,
        public string $notificationType // 'post_author' o 'comment_reply'
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = new MailMessage();

        if ($this->notificationType === 'post_author') {
            $message->line('Usuario "' . $this->comment->user->username . '" ha comentado en tu publicación:')
                ->line('"' . Str::limit($this->comment->comment, 100) . '"');
        } else {
            $message->line('Usuario "' . $this->comment->user->username . '" ha respondido a tu comentario:')
                ->line('"' . Str::limit($this->comment->comment, 100) . '"');
        }

        return $message->action('Ver publicación', url(route('post.view', $this->post->id)))
            ->line('¡Gracias por usar nuestra aplicación!');
    }

    public function toDatabase(object $notifiable): array
    {
        $baseData = [
            'comment_id' => $this->comment->id,
            'post_id' => $this->post->id,
            'comment_author' => $this->comment->user->username,
            'comment_excerpt' => Str::limit($this->comment->comment, 100),
            'link' => route('post.view', $this->post->id),
            'user_avatar' => $this->comment->user->profile_photo_url ?? null,
            'icon' => 'comment'
        ];

        if ($this->notificationType === 'post_author') {
            return array_merge($baseData, [
                'type' => 'post_comment',
                'title' => 'Nuevo comentario en tu publicación',
                'message' => $this->comment->user->username . ' comentó: "' . Str::limit($this->comment->comment, 100) . '"'
            ]);
        } else {
            return array_merge($baseData, [
                'type' => 'comment_reply',
                'title' => 'Respondieron a tu comentario',
                'message' => $this->comment->user->username . ' respondió: "' . Str::limit($this->comment->comment, 100) . '"',
                'parent_comment_id' => $this->comment->parent_id,
                'parent_comment_author' => $this->comment->parent->user->username
            ]);
        }
    }
}
