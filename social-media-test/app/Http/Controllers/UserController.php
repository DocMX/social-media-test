<?php

namespace App\Http\Controllers;

use App\Models\Follower;
use App\Models\User;
use App\Notifications\FollowUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function follow(Request $request, User $user)
    {
        $request->validate([
            'follow' => ['required', 'boolean']
        ]);

        $follower = Auth::user();
        
        // Evitar auto-seguimiento
        if ($user->id === $follower->id) {
            return back()->with('error', 'No puedes seguirte a ti mismo');
        }

        $followStatus = $request->input('follow');
        $alreadyFollowing = Follower::where([
            'user_id' => $user->id,
            'follower_id' => $follower->id
        ])->exists();

        if ($followStatus) {
            if ($alreadyFollowing) {
                return back()->with('info', 'Ya sigues a este usuario');
            }

            Follower::create([
                'user_id' => $user->id,
                'follower_id' => $follower->id
            ]);
            $message = 'Ahora sigues a '.$user->username;
        } else {
            if (!$alreadyFollowing) {
                return back()->with('info', 'No sigues a este usuario');
            }

            Follower::where([
                'user_id' => $user->id,
                'follower_id' => $follower->id
            ])->delete();
            $message = 'Dejaste de seguir a '.$user->username;
        }

        // Enviar notificación solo si es un follow (no para unfollow)
        if ($followStatus) {
            $user->notify(new FollowUser($follower, true));
        }

        return back()->with('success', $message);
    }
}