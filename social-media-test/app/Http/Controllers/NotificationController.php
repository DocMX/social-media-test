<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    // Obtener notificaciones
    public function index(Request $request)
    {
        $user = $request->user();

        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function ($notification) {
                // Asegúrate que todos los campos necesarios estén presentes
                $data = $notification->data;

                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'data' => array_merge([
                        'title' => $data['title'] ?? 'Notificación',
                        'message' => $data['message'] ?? '',
                        'link' => $data['link'] ?? '#',
                        'comment_id' => $data['comment_id'] ?? null,
                        'post_id' => $data['post_id'] ?? null,
                        'comment_author' => $data['comment_author'] ?? null,
                        'comment_excerpt' => $data['comment_excerpt'] ?? null,
                        'post_title' => $data['post_title'] ?? null,
                        'user_avatar' => $data['user_avatar'] ?? null
                    ], $data),
                    'read_at' => $notification->read_at,
                    'created_at' => $notification->created_at
                ];
            });

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $user->unreadNotifications()->count(),
        ]);
    }

    // Marcar una notificación como leída
    public function markAsRead(Request $request, string $notificationId)
    {
        $notification = DatabaseNotification::findOrFail($notificationId);

        // Verificamos que la notificación le pertenezca al usuario
        if ($notification->notifiable_id !== $request->user()->id) {
            abort(403, 'No autorizado');
        }

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    // Marcar todas como leídas
    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json(['success' => true]);
    }
}
