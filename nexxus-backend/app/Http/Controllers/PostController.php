<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{

    public function index()
    {
        $posts = Post::with(['likes', 'comments.user']) // Incluir comentarios y usuarios
            ->get()
            ->map(function ($post) {
                $post->liked_by_user = $post->likes->contains('user_id', auth()->id());
                $post->likes_count = $post->likes->count();
                return $post;
            });

        return response()->json([
            'message' => 'Posts retrieved successfully',
            'data' => $posts,
        ], 200);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'description' => 'nullable|string',
            'publish_date' => 'required|date',
            'id_user' => 'required|integer|exists:users,id',
            'file' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:20480',
        ]);

        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('uploads', 'public');
            $validatedData['file_path'] = $filePath; // Agregar el campo manualmente
        }

        // Asegurar que el modelo tenga el campo 'file_path'
        $post = Post::create(array_merge($validatedData, [
            'file_path' => $validatedData['file_path'] ?? null // Agregarlo si no existe
        ]));

        return response()->json([
            'message' => 'Post created successfully',
            'data' => $post,
        ], 201);
    }

    public function destroy($id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'message' => 'Post not found',
            ], 404);
        }

        $post->delete();

        return response()->json([
            'message' => 'Post deleted successfully',
        ], 200);
    }
}
