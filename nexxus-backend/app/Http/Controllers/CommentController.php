<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

    public function destroy($id)
    {
        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json(['message' => 'Comment not found'], 404);
        }

        if ($comment->user_id != auth()->id()) {
            return response()->json(['message' => 'Unauthorized: You cannot delete this comment'], 401);
        }

        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully'], 200);
    }


    public function update(Request $request, $commentId)
    {
        // Debug incoming data
        // Log::info('Update request received', [
        //     'content' => $request->input('content'),
        //     'all' => $request->all()
        // ]);

        $comment = Comment::find($commentId);

        if (!$comment) {
            // Log::error('Comment not found', ['comment_id' => $commentId]);
            return response()->json(['message' => 'Comment not found'], 404);
        }

        if ($comment->user_id != auth()->id()) {
            // Log::error('Unauthorized: You cannot edit this comment', ['user_id' => auth()->id(), 'comment_id' => $comment->id]);
            return response()->json(['message' => 'Unauthorized: You cannot edit this comment'], 401);
        }

        $request->validate([
            'content' => 'required|string|max:255',
        ]);

        $comment->content = $request->input('content');
        $comment->save();

        // Log::info('Comment updated successfully', ['comment' => $comment]);

        return response()->json([
            'message' => 'Comment updated successfully',
            'comment' => $comment
        ], 200);
    }

}
