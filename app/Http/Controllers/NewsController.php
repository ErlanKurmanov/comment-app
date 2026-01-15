<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use App\Http\Resources\NewsResource;
use App\Http\Resources\CommentResource;

class NewsController extends Controller
{
    public function index()
    {
        return News::select('id', 'title', 'description')->paginate(10);
    }

    public function store(Request $request)
    {
        $news = News::create($request->validate([
            'title' => 'required', 'description' => 'required'
        ]));
        return response()->json($news, 201);
    }

    public function show(News $news): JsonResponse
    {
        $rootComments = $news->comments()
            ->whereNull('parent_id')
            ->with(['user', 'replies.user', 'replies.replies.user'])
            ->orderByDesc('id')
            ->cursorPaginate(15);

        return response()->json([
            'news' => $news,
            'comments' => CommentResource::collection($rootComments),
            'meta' => [
                'next_cursor' => $rootComments->nextCursor()?->encode(),
                'prev_cursor' => $rootComments->previousCursor()?->encode(),
            ]
        ]);
    }
}
