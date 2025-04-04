<?php

namespace App\Http\Controllers;

use App\Models\Story;
use App\Models\Like;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StoryController extends Controller
{
    public function index()
    {
        $stories = Story::all();

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
            'file' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:20480',
        ]);

        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('uploads', 'public');
            $validatedData['file_path'] = $filePath;
        }

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

        // Eliminar l'arxiu associat a la història si existeix
        if ($story->file_path && Storage::exists('public/' . $story->file_path)) {
            Storage::delete('public/' . $story->file_path);
        }

        // Eliminar la història
        $story->delete();

        return response()->json([
            'message' => 'Story deleted successfully',
        ], 200);

    }
}