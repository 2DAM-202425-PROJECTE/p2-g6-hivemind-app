<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $chats = Chat::where('is_group', false)
            ->whereHas('users', fn($q) => $q->where('user_id', $user->id))
            ->with(['users' => fn($q) => $q->select('users.id', 'users.name', 'users.username', 'users.profile_photo_path')])
            ->latest('updated_at')
            ->get();

        return response()->json([
            'chats' => $chats->map(function ($chat) {
                return [
                    'id' => $chat->id,
                    'name' => $chat->name,
                    'users' => $chat->users->map(function ($user) {
                        return [
                            'id' => $user->id,
                            'name' => $user->name,
                            'username' => $user->username,
                            'profile_photo_url' => $user->profile_photo_path, // Maps to profile_photo_path
                        ];
                    }),
                ];
            }),
        ]);
    }

    public function getPrivateChat(Request $request)
    {
        $request->validate([
            'recipient_id' => 'required|integer|exists:users,id',
        ]);

        $userId = Auth::id();
        $recipientId = $request->input('recipient_id');

        if ($userId === $recipientId) {
            return response()->json(['error' => 'Cannot start a chat with yourself'], 403);
        }

        $firstId = min($userId, $recipientId);
        $secondId = max($userId, $recipientId);

        Log::info("[$userId] Buscant xat privat entre $firstId i $secondId");

        $chat = Chat::where('is_group', false)
            ->whereHas('users', fn($query) => $query->where('user_id', $firstId))
            ->whereHas('users', fn($query) => $query->where('user_id', $secondId))
            ->first();

        if (!$chat) {
            $chat = $this->createPrivateChat($userId, $firstId, $secondId);
        }

        return response()->json([
            'chat' => [
                'id' => $chat->id,
                'name' => $chat->name,
                'users' => $chat->users->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'username' => $user->username,
                        'profile_photo_url' => $user->profile_photo_path,
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
        $chat = Chat::create([
            'is_group' => false,
            'is_server' => false,
            'created_by' => $userId,
            'name' => "PrivateChat-$firstId-$secondId",
        ]);
        $chat->users()->attach([$firstId, $secondId]);
        return $chat;
    }
}
