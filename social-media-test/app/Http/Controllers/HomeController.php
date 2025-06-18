<?php

namespace App\Http\Controllers;

use App\Http\Enums\GroupPrivacyEnum;
use App\Http\Enums\GroupUserStatus;
use App\Http\Resources\GroupResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Models\Group;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $user = $request->user();

        // Obtener publicaciones del timeline
        $posts = Post::postsForTimeline($userId)
            ->select('posts.*')
            ->leftJoin('followers AS f', function ($join) use ($userId) {
                $join->on('posts.user_id', '=', 'f.user_id')
                    ->where('f.follower_id', '=', $userId);
            })
            ->leftJoin('group_users AS gu', function ($join) use ($userId) {
                $join->on('gu.group_id', '=', 'posts.group_id')
                    ->where('gu.user_id', '=', $userId)
                    ->where('gu.status', GroupUserStatus::APPROVED->value);
            })
            ->where(function ($query) {
                $query->whereNull('posts.group_id')
                    ->orWhereNotNull('gu.id');
            })
            ->where(function ($query) use ($userId) {
                $query->whereNotNull('f.id')
                    ->orWhere('posts.user_id', $userId);
            })
            ->latest()
            ->paginate(10);

        $posts = PostResource::collection($posts);

        // Grupos del usuario
        $groups = Group::query()
            ->with('currentUserGroup')
            ->select(['groups.*'])
            ->join('group_users AS gu', 'gu.group_id', 'groups.id')
            ->where('gu.user_id', $userId)
            ->orderBy('gu.role')
            ->orderBy('name', 'desc')
            ->get();

        // IDs de grupos donde ya es miembro
        $userGroupIds = DB::table('group_users')
            ->where('user_id', $userId)
            ->where('status', GroupUserStatus::APPROVED->value)
            ->pluck('group_id')
            ->toArray();

        // Grupos recomendados (públicos y no miembros)
        $recommendedGroups = Group::query()
            ->where('privacy', GroupPrivacyEnum::PRIVACY_PUBLIC->value)
            ->whereNotIn('id', $userGroupIds)
            ->withCount('approvedUsers')
            ->orderBy('approved_users_count', 'desc')
            ->take(5)
            ->get();

        // Publicaciones recomendadas (no seguidos)
        $recommendedPosts = Post::query()
            ->select('posts.*')
            ->whereNotIn('user_id', function ($query) use ($userId) {
                $query->select('user_id')
                    ->from('followers')
                    ->where('follower_id', $userId);
            })
            ->where('user_id', '!=', $userId)
            ->withCount('reactions')
            ->orderBy('reactions_count', 'desc')
            ->take(5)
            ->get();

        return Inertia::render('Home', [
            'posts' => $posts,
            'groups' => GroupResource::collection($groups),
            'followings' => UserResource::collection($user->followings),
            'recommendedGroups' => GroupResource::collection($recommendedGroups),
            'recommendedPosts' => PostResource::collection($recommendedPosts),
        ]);
    }
}
