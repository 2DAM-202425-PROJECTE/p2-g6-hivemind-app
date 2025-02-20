<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Post $post) // Debe inyectar el modelo Post
    {
        $request->validate([
            'content' => 'required|string|max:255',
        ]);
    
        $comment = $post->comments()->create([
            'content' => $request->content,
            'user_id' => auth()->id()
        ]);
    
        return response()->json($comment->load('user'), 201);
    }

    public function index(Post $post)
    {
        return response()->json($post->comments()->with('user')->get());
    }

    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);
        $comment->delete();

        return response()->json(null, 204);
    }
}
