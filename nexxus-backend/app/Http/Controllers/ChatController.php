<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    /**
     * Obtiene un chat privado entre dos usuarios si existe.
     */
    public function getPrivateChat(Request $request)
    {
        $request->validate([
            'recipient_id' => 'required|integer|exists:users,id',
        ]);

        $userId = Auth::id();
        $recipientId = $request->input('recipient_id');

        // Ordenar IDs per assegurar sempre el mateix format
        $firstId = min($userId, $recipientId);
        $secondId = max($userId, $recipientId);

        Log::info("[$userId] Buscant xat privat entre $firstId i $secondId");

        // Buscar exactament un xat entre aquests dos usuaris
        $chat = Chat::where('is_group', false)
            ->whereHas('users', function ($query) use ($firstId, $secondId) {
                $query->where('user_id', $firstId);
            })
            ->whereHas('users', function ($query) use ($firstId, $secondId) {
                $query->where('user_id', $secondId);
            })
            ->first();

        if (!$chat) {
            $chat = $this->createPrivateChat($userId, $firstId, $secondId);
        }

        return response()->json([
            'chat' => [
                'id' => $chat->id,
                'name' => $chat->name, // Aquí retorna el nom correcte del xat
                // user data
                'users' => $chat->users->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'username' => $user->username,
                        'profile_photo_url' => $user->profile_photo_url,
                    ];
                }),
            ]
        ]);
    }

    /**
     * Crea un chat privado entre dos usuarios si no existe.
     */
    public function createPrivateChat($userId, $firstId, $secondId)
    {
        Log::info("[$userId] Creant xat privat entre $firstId i $secondId");

        // Crear el xat
        $chat = Chat::create([
            'is_group' => false,
            'is_server' => false,
            'created_by' => $userId,
            'name' => "PrivateChat-$firstId-$secondId",
        ]);

        // Afegir usuaris al xat
        $chat->users()->attach([$firstId, $secondId]);

        return $chat;
    }
}
