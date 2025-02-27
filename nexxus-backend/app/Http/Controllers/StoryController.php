<?php

namespace App\Http\Controllers;

use App\Models\Story;
use App\Models\Like;
use App\Models\Comment;
use Illuminate\Http\Request;

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
            $validatedData['file_path'] = $filePath; // Agregar el campo manualmente
        }

        // Asegurar que el modelo tenga el campo 'file_path'
        $story = Story::create(array_merge($validatedData, [
            'file_path' => $validatedData['file_path'] ?? null // Agregarlo si no existe
        ]));

        return response()->json([
            'message' => 'Story created successfully',
            'data' => $story,
        ], 201);
    }
}