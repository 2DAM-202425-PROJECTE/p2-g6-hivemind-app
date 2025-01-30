<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'content' => 'required|string|max:255',
            'publish_date' => 'required|date',
            'id_user' => 'required|integer|exists:users,id',
        ]);

        $post = Post::create($validatedData);

        return response()->json([
            'message' => 'Post created successfully',
            'data' => $post,
        ], 201);
    }
}
