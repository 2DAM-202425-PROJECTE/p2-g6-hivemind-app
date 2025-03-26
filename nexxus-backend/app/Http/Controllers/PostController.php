<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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
            'file' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,mp4|max:100000',
            'location' => 'nullable|string',
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

    public function update(Request $request, $postId)
    {
        // Debug incoming data
        Log::info('Update request received', [
            'description' => $request->input('description'),
            'location' => $request->input('location'),
            'hasFile' => $request->hasFile('file'),
            'all' => $request->all()
        ]);

        $post = Post::find($postId);

        if (!$post) {
            Log::error('Post not found', ['post_id' => $postId]);
            return response()->json(['message' => 'Post not found'], 404);
        }

        if ($post->id_user != auth()->id()) {
            Log::error('Unauthorized: You cannot edit this post', ['user_id' => auth()->id(), 'post_id' => $post->id]);
            return response()->json(['message' => 'Unauthorized: You cannot edit this post'], 401);
        }

        $request->validate([
            'description' => 'nullable|string|max:1000',
            'location' => 'nullable|string',
            'file' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,mp4|max:500000',
        ]);

        if ($request->has('description')) {
            Log::info('Updating description', ['description' => $request->description]);
            $post->description = $request->description;
        }

        if ($request->has('location')) {
            Log::info('Updating location', ['location' => $request->location]);
            $post->location = $request->location;
        }

        if ($request->hasFile('file')) {
            Log::info('Updating file', ['file' => $request->file('file')->getClientOriginalName()]);
            if ($post->file_path && Storage::disk('public')->exists($post->file_path)) {
                Storage::disk('public')->delete($post->file_path);
            }

            $filePath = $request->file('file')->store('uploads', 'public');
            $post->file_path = $filePath;
        }

        Log::info('Post before save', ['post' => $post]);

        $post->save();

        Log::info('Post after save', ['post' => $post]);

        Log::info('Post updated successfully', ['post' => $post]);

        return response()->json([
            'message' => 'Post updated successfully',
            'post' => $post
        ], 200);
    }

    public function show($id)
    {
        $post = Post::with(['likes', 'comments.user'])->find($id);

        if (!$post) {
            return response()->json([
                'message' => 'Post not found',
            ], 404);
        }

        $post->liked_by_user = $post->likes->contains('user_id', auth()->id());
        $post->likes_count = $post->likes->count();

        return response()->json([
            'message' => 'Post retrieved successfully',
            'data' => $post,
        ], 200);

    }
    public function destroy($id)
    {

        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'message' => 'Post not found',
            ], 404);
        }

        // Eliminar el POST DEL USUARIO LOGUEADO
        if ($post->id_user == auth()->id()) {
            $post->delete();
            return response()->json([
                'message' => 'Post deleted successfully',
            ], 200);
        } else {
            return response()->json([
                'message' => 'Unauthorized: You cannot delete this post',
            ], 401);
        }
    }
}
