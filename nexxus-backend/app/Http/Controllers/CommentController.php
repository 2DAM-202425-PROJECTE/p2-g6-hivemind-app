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
            'media' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,mov|max:10240', // Validate media_path if needed
            'parent_id' => 'nullable|exists:comments,id', // Ensure parent_id is validated
        ]);

        $mediaPath = null;

        if ($request->hasFile('media')) {
            $mediaPath = $request->file('media')->store('uploads', 'public');
        }
    
        $comment = $post->comments()->create([
            'content' => $request->content,
            'user_id' => auth()->id(),
            'post_id' => $request->input('post_id'),
            'media_path' => $mediaPath, // Save media_path if provided
            'parent_id' => $request->parent_id, // Save parent_id if provided
        ]);
    
        return response()->json($comment->load('user', 'replies.user'), 201); // Load replies for nested structure
    }

    public function index(Post $post)
    {
        $comments = $post->comments()
            ->whereNull('parent_id')
            ->with(['user', 'replies.user'])
            ->get();

        return response()->json($comments);
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
