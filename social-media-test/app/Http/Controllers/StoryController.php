<?php

namespace App\Http\Controllers;

use App\Models\Story;
use App\Http\Requests\StoreStoriesRequest;
use App\Http\Resources\StoriesResource;
use Illuminate\Http\Request;

class StoryController extends Controller
{
    public function index()
    {
        $stories = Story::with('user')
            ->where('expires_at', '>', now())
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
        $media = $request->file('media');
        $path = $media->store('stories', 'public');
        $type = $media->getMimeType();
        $mediaType = str_contains($type, 'video') ? 'video' : 'image';

        $story = Story::create([
            'user_id' => auth()->id(),
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
}
