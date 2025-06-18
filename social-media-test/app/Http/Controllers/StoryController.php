<?php

namespace App\Http\Controllers;

use App\Models\Story;
use App\Http\Requests\StoreStoriesRequest;
use App\Http\Resources\StoriesResource;
use App\Models\StoryView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


class StoryController extends Controller
{
    public function index()
    {
        $stories = Story::with('user')
            ->where('expires_at', '>', now())
            ->withCount('views')
            ->latest()
            ->get()
            ->groupBy('user_id')
            ->map(function ($groupedStories) {
                return StoriesResource::collection($groupedStories);
            });

        return response()->json($stories);
    }


    public function store(StoreStoriesRequest $request)
    {
        $user = Auth::user();
        $media = $request->file('media');
        $path = $media->store('stories', 'public');
        $type = $media->getMimeType();
        $mediaType = str_contains($type, 'video') ? 'video' : 'image';

        $story = Story::create([
            'user_id' => $user->id,
            'media_path' => $path,
            'media_type' => $mediaType,
            'caption' => $request->input('caption'),
            'expires_at' => now()->addHours(24),
        ]);

        return response()->json([
            'message' => 'Historia creada correctamente',
            'story' => new StoriesResource($story),
        ]);
    }

    public function markAsViewed(Request $request)
    {
        $user = Auth::user();
        $storyId = $request->input('story_id');

        StoryView::firstOrCreate([
            'story_id' => $storyId,
            'user_id' => $user->id,
        ], [
            'viewed_at' => now(),
        ]);

        return response()->json(['status' => 'ok']);
    }
}
