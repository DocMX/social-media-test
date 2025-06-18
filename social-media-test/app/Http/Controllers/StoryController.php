<?php

namespace App\Http\Controllers;

use App\Models\Story;
use Illuminate\Http\Request;

class StoryController extends Controller
{
    public function index()
    {
        $stories = Story::with('user')
            ->where('expires_at', '>', now())
            ->latest()
            ->get()
            ->groupBy('user_id');

        return response()->json($stories);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'media' => 'required|file|mimes:jpeg,png,jpg,mp4|max:10240',
            'caption' => 'nullable|string|max:255',
        ]);

        $media = $request->file('media');
        $path = $media->store('stories', 'public');
        $type = $media->getMimeType();
        $mediaType = str_contains($type, 'video') ? 'video' : 'image';

        $story = Story::create([
            'user_id' => auth()->id(),
            'media_path' => $path,
            'media_type' => $mediaType,
            'caption' => $data['caption'] ?? null,
            'expires_at' => now()->addHours(24),
        ]);

        return response()->json(['message' => 'Story created!', 'story' => $story]);
    }
}
