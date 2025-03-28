<?php

namespace App\Http\Controllers;

use App\Http\Requests\Comment\StoreCommentRequest;
use App\Http\Requests\Comment\UpdateCommentRequest;
use App\Models\Comment;
use App\Models\Post;
use Gate;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function __construct()
    {
        Gate::resource('comment', Comment::class);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCommentRequest $request, Post $post)
    {
        Gate::authorize('create', Comment::class);

        Comment::create([
            ...$request->validated(),
            'post_id' => $post->id,
            'user_id' => $request->user()->id,
        ]);

        return redirect($post->showRoute())
            ->banner('Your comment has been created.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCommentRequest $request, Comment $comment)
    {
        Gate::authorize('update', $comment);

        $comment->update([
            'body' => $request->validated('body'),
        ]);

        return redirect($comment->post->showRoute(['page' => $request->query('page')]))
            ->banner('Your comment has been updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Comment $comment)
    {
        Gate::authorize('delete', $comment);

        $comment->delete();

        return redirect($comment->post->showRoute(['page' => $request->query('page')]))
            ->banner('Your comment has been deleted.');
    }
}
