<?php

namespace App\Http\Controllers;

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
            ->latest()
            ->paginate(10);

        $posts = PostResource::collection($posts);
        if ($request->wantsJson()) {
            return $posts;
        }

        $groups = Group::query()
            ->with('currentUserGroup')
            ->select(['groups.*'])
            ->join('group_users AS gu', 'gu.group_id', 'groups.id')
            ->where('gu.user_id', $userId)
            ->orderBy('gu.role')
            ->orderBy('name', 'desc')
            ->get();


        $userGroupIds = DB::table('group_users')
            ->where('user_id', $userId)
            ->where('status', GroupUserStatus::APPROVED->value)
            ->pluck('group_id')
            ->toArray();

        $recommendedGroups = Group::query()
            ->where('privacy', Group::PRIVACY_PUBLIC)
            ->whereNotIn('id', $userGroupIds)
            ->withCount('approvedUsers')
            ->orderBy('approved_users_count', 'desc')
            ->take(5)
            ->get();

        return Inertia::render('Home', [
            'posts' => $posts,
            'groups' => GroupResource::collection($groups),
            'followings' => UserResource::collection($user->followings),
            'recommendedGroups' => GroupResource::collection($recommendedGroups)
        ]);
    }
}
