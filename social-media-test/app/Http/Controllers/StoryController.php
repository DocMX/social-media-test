<?php

namespace App\Http\Controllers;

use App\Models\Story;
use App\Http\Requests\StoreStoriesRequest;
use App\Http\Resources\StoriesResource;
use App\Models\StoryView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use FFMpeg\FFMpeg;
use FFMpeg\Coordinate\TimeCode;


class StoryController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $followedUserIds = $user->followings()->pluck('users.id');

        // 👇 Esto viene después de definirla
        $followedUserIds->push($user->id);

        // Filtrar historias solo de los usuarios seguidos (y que no hayan expirado)
        $stories = Story::with([
            'user',
            'viewers' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }
        ])
            ->whereIn('user_id', $followedUserIds)
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
        $extension = $media->getClientOriginalExtension();
        $videoExtensions = ['mp4', 'mov', 'avi', 'webm', 'mkv'];
        $mediaType = in_array(strtolower($extension), $videoExtensions) ? 'video' : 'image';

        

        $previewPath = null;

        if ($mediaType === 'video') {
            try {
                $ffmpeg = FFMpeg::create([
                    'ffmpeg.binaries'  => env('FFMPEG_PATH', 'ffmpeg'),
                    'ffprobe.binaries' => env('FFPROBE_PATH', 'ffprobe'),
                ]);

                $video = $ffmpeg->open(storage_path("app/public/{$path}"));

                $previewFileName = 'previews/' . uniqid() . '.jpg';
                $fullPreviewPath = storage_path("app/public/{$previewFileName}");

                $frame = $video->frame(TimeCode::fromSeconds(1));
                $frame->save($fullPreviewPath);

                $previewPath = $previewFileName;
            } catch (\Exception $e) {
                dd('Error creando miniatura', $e->getMessage());
            }
        }



        $story = Story::create([
            'user_id' => $user->id,
            'media_path' => $path,
            'preview_path' => $previewPath,
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
