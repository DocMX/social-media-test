<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $userId = $user->id;

        // Conversaciones pasadas
        $conversations = Message::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->with(['sender', 'receiver'])
            ->get()
            ->map(function ($message) use ($userId) {
                $other = $message->sender_id === $userId ? $message->receiver : $message->sender;
                return [
                    'user_id' => $other->id,
                    'name' => $other->name,
                    'avatar' => $other->avatar_path ? '/storage/' . $other->avatar_path : '/img/default-avatar.jpg',
                    'last_message' => $message->body,
                    'date' => $message->created_at->diffForHumans(),
                ];
            })
            ->unique('user_id')
            ->values();

        // Cargar mensajes con el primer usuario si existe
        $firstUser = $conversations->first();

        $messages = collect();
        if ($firstUser) {
            $messages = Message::where(function ($q) use ($userId, $firstUser) {
                $q->where('sender_id', $userId)->where('receiver_id', $firstUser['user_id']);
            })->orWhere(function ($q) use ($userId, $firstUser) {
                $q->where('sender_id', $firstUser['user_id'])->where('receiver_id', $userId);
            })->orderBy('created_at')->get();
        }

        //  Usuarios que sigo
        $followingUsers = $user->followings()->select('users.id', 'users.name')->get();


        return Inertia::render('Messages/Index', [
            'conversations' => $conversations,
            'messages' => $messages,
            'selectedUser' => $firstUser,
            'followingUsers' => $followingUsers, // ← Aquí los usuarios seguidos
        ]);
    }




    public function store(Request $request)
    {
        $validated = $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'body' => 'required|string',
        ]);

        Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $validated['receiver_id'],
            'body' => $validated['body'],
        ]);

        return redirect()->route('messages')->with('success', 'Mensaje enviado con éxito');
    }

    public function conversations(Request $request)
    {
        $user = $request->user();
        $userId = $user->id;

        // Obtener usuarios con los que el usuario ha conversado (enviando o recibiendo)
        $conversations = Message::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->with(['sender', 'receiver'])
            ->get()
            ->map(function ($message) use ($userId) {
                // El otro participante
                $other = $message->sender_id === $userId ? $message->receiver : $message->sender;
                return [
                    'user_id' => $other->id,
                    'name' => $other->name,
                    'avatar' => $other->avatar_path ? '/storage/' . $other->avatar_path : '/img/default-avatar.jpg',
                    'last_message' => $message->body,
                    'date' => $message->created_at->diffForHumans(),
                ];
            })
            // Agrupar por usuario para evitar duplicados
            ->unique('user_id')
            ->values();

            $selectedUser = $conversations->first() ?? null;

        return Inertia::render('Messages/Conversations', [
            'conversations' => $conversations,
            'selectedUser' => $selectedUser,
        ]);
    }
    public function chat(Request $request, User $user)
    {
        $authUser = $request->user();
        $authId = $authUser->id;

        $messages = Message::where(function ($q) use ($authId, $user) {
            $q->where('sender_id', $authId)->where('receiver_id', $user->id);
        })->orWhere(function ($q) use ($authId, $user) {
            $q->where('sender_id', $user->id)->where('receiver_id', $authId);
        })
            ->orderBy('created_at')
            ->get();

        return response()->json([
            'messages' => $messages
        ]);
    }
}
