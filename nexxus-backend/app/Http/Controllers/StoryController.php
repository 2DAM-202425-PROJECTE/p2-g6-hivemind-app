<?php

namespace App\Http\Controllers;

use App\Models\Story;
use App\Models\Like;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class StoryController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $profileUserId = $request->query('profile_user_id'); // Get profile user ID from query
        $role = $request->query('role') ?? $user->role; // Get role from query or authenticated user

        $query = Story::query();

        // Filter by profile user ID if provided
        if ($profileUserId) {
            $query->where('id_user', $profileUserId);
        }

        // Filter by role
        if ($role) {
            $query->byRole($role);
        }

        $stories = $query->get();

        return response()->json([
            'message' => 'Stories retrieved successfully',
            'data' => $stories,
        ], 200);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'description' => 'nullable|string',
            'publish_date' => 'required|date',
            'id_user' => 'required|integer|exists:users,id',
            'file' => 'required|file|mimes:jpeg,png,jpg,gif,svg,mp4|max:20480',
            'role' => 'nullable|string|in:test_unit,admin', // Validate role
        ]);

        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('uploads', 'public');
            $validatedData['file_path'] = $filePath;
        }

        // Automatically set the role to the authenticated user's role if not provided
        $validatedData['role'] = $validatedData['role'] ?? Auth::user()->role;

        $story = Story::create($validatedData);

        return response()->json([
            'message' => 'Story created successfully',
            'data' => $story,
        ], 201);
    }

    public function destroy($id)
    {
        $story = Story::find($id);

        if (!$story) {
            return response()->json([
                'message' => 'Story not found',
            ], 404);
        }

        // Ensure the user can only delete their own stories
        if ($story->id_user !== Auth::id()) {
            return response()->json([
                'message' => 'Unauthorized to delete this story',
            ], 403);
        }

        // Delete the associated file
        if ($story->file_path && Storage::exists('public/' . $story->file_path)) {
            Storage::delete('public/' . $story->file_path);
        }

        $story->delete();

        return response()->json([
            'message' => 'Story deleted successfully',
        ], 200);
    }
}
