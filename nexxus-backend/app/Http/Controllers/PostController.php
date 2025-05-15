<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{

    public function index(Request $request)
    {
        $posts = Post::with(['likes', 'comments.user'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Transformar los posts manteniendo la estructura del paginador
        $posts->getCollection()->transform(function ($post) {
            $post->liked_by_user = $post->likes->contains('user_id', auth()->id());
            $post->likes_count = $post->likes->count();
            return $post;
        });

        return response()->json([
            'message' => 'Posts retrieved successfully',
            'data' => $posts->items(),
            'current_page' => $posts->currentPage(),
            'last_page' => $posts->lastPage(),
            'per_page' => $posts->perPage(),
            'total' => $posts->total(),
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
            // Store the uploaded file and get its path
            $filePath = $request->file('file')->store('uploads', 'public');
            $validatedData['file_path'] = $filePath;

            // Generate thumbnail if the file is a video
            if ($request->file('file')->getClientOriginalExtension() === 'mp4') {
                $thumbnailPath = 'uploads/thumbnails/' . pathinfo($filePath, PATHINFO_FILENAME) . '.jpg';
                $videoFullPath = storage_path('app/public/' . $filePath);
                $thumbnailFullPath = storage_path('app/public/' . $thumbnailPath);

                $thumbnailsDir = storage_path('app/public/uploads/thumbnails');
                if (!file_exists($thumbnailsDir)) {
                    mkdir($thumbnailsDir, 0755, true);
                }
            
                // Log paths for debugging
                Log::info('Video full path:', ['path' => $videoFullPath]);
                Log::info('Thumbnail full path:', ['path' => $thumbnailFullPath]);
            
                // Generate thumbnail using FFmpeg
                $command = "/usr/bin/ffmpeg -i $videoFullPath -ss 00:00:01 -vframes 1 -update 1 $thumbnailFullPath";
                Log::info('Executing FFmpeg command:', ['command' => $command]);
                exec($command, $output, $returnVar);
                Log::info('FFmpeg output:', ['output' => $output, 'returnVar' => $returnVar]);
            
                if ($returnVar === 0) {
                    $validatedData['thumbnail_path'] = $thumbnailPath;
                } else {
                    Log::error('FFmpeg failed to generate thumbnail', ['output' => $output, 'returnVar' => $returnVar]);
                }
            }
        }

        // Create the post
        $post = Post::create(array_merge($validatedData, [
            'file_path' => $validatedData['file_path'] ?? null,
            'thumbnail_path' => $validatedData['thumbnail_path'] ?? null,
        ]));

        // Notify other users
        $users = User::all();
        foreach ($users as $user) {
            if ($user->id !== auth()->id()) {
                Notification::create([
                    'user_id' => $user->id,
                    'post_id' => $post->id,
                    'message' => auth()->user()->name . ' ha publicado un nuevo post',
                ]);
            }
        }

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
