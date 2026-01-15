<?php

namespace App\Http\Controllers;

use App\Models\VideoPost;
use Illuminate\Http\Request;

class VideoPostController extends Controller
{
    public function index()
    {
        return VideoPost::select('id', 'title', 'description')->paginate(10);
    }

    public function store(Request $request)
    {
        $videoPost = VideoPost::create($request->validate([
            'title' => 'required',
            'description' => 'required',
        ]));

        return response()->json($videoPost, 201);
    }

    public function show(VideoPost $videoPost): JsonResponse
    {
        $rootComments = $videoPost->comments()
            ->whereNull('parent_id')
            ->with([
                'user',
                'replies.user',
                'replies.replies.user',
            ])
            ->orderByDesc('id')
            ->cursorPaginate(15);

        return response()->json([
            'video_post' => $videoPost,
            'comments' => CommentResource::collection($rootComments),
            'meta' => [
                'next_cursor' => $rootComments->nextCursor()?->encode(),
                'prev_cursor' => $rootComments->previousCursor()?->encode(),
            ],
        ]);
    }
}
