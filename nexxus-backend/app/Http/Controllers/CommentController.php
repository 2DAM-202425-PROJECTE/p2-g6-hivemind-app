<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store (Request $request) {
        $validatedData = $request->validate([
            'id_post' => 'required|integer|exists:post,id',
            'id_user' => 'required|integer|exists:users,id',
            'comment_text' => 'required|string|max:255',
            'comment_date' => 'required|date',
        ]);

        $comment = Comment::create($validatedData);

        return response()->json([
            'message' => 'Comment created successfully',
            'data' => $comment,
        ], 201);
    }
}
