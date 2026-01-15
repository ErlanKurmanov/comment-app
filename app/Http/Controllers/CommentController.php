<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(StoreCommentRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

        $comment = Comment::create($data);

        $comment->load('user');

        return new CommentResource($comment);
    }

    public function update(Request $request, Comment $comment)
    {
        if ($request->user()->id !== $comment->user_id) {
            abort(403, 'Unauthorized');
        }

        $request->validate(['body' => 'required|string']);
        $comment->update(['body' => $request->body]);

        return new CommentResource($comment);
    }

    public function destroy(Request $request, Comment $comment)
    {
        if ($request->user()->id !== $comment->user_id) {
            abort(403, 'Unauthorized');
        }

        $comment->delete();
        return response()->noContent();
    }
}
