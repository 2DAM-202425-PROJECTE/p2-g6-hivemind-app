<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Like;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;

class LikeController extends Controller
{
   // LikeController.php
public function store(Request $request, $id) // ✅ Recibe $id desde la ruta
{
    // Validar que el post existe
    if (!Post::where('id', $id)->exists()) {
        return response()->json(['message' => 'Post no encontrado'], 404);
    }
    // Evitar likes duplicados
    $existingLike = Like::where('user_id', Auth::id())
        ->where('post_id', $id)
        ->exists();

    if ($existingLike) {
        return response()->json(['message' => 'Ya has dado like a este post'], 409);
    }

    // Crear el like
    Like::create([
        'user_id' => Auth::id(),
        'post_id' => $id, // ✅ Usa $id desde la URL
    ]);

    return response()->json(['message' => 'Like registrado']);
}

public function destroy(Request $request, $id) // ✅
{
    $like = Like::where('user_id', Auth::id())
        ->where('post_id', $id) // ✅ Usa $id desde la URL
        ->first();

    if ($like) {
        $like->delete();
        return response()->json(['message' => 'Like eliminado']);
    }

    return response()->json(['message' => 'Like no encontrado'], 404);
}
}
