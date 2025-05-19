<?php

namespace App\Http\Controllers;

use App\Events\MessageDeletedEvent;
use App\Events\MessageEditedEvent;
use App\Events\MessageSentEvent;
use App\Models\Chat;
use App\Models\Message;
use App\Models\MessageHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function getMessages($chatName)
    {
        $chat = Chat::where('name', $chatName)->first();

        if (!$chat) {
            return response()->json(['error' => 'El chat no existeix'], 404);
        }

        $messages = Message::where('chat_id', $chat->id)
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark messages as read
        Message::where('chat_id', $chat->id)
            ->where('user_id', '!=', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages->load('user');

        return response()->json(['messages' => $messages->map(function ($message) {
            return [
                'id' => $message->id,
                'content' => $message->content,
                'user_id' => $message->user_id,
                'user' => [
                    'id' => $message->user->id,
                    'name' => $message->user->name,
                    'profile_photo_url' => $message->user->profile_photo_url,
                ],
                'created_at' => $message->created_at->toDateTimeString(),
                'is_edited' => $message->is_edited,
                'is_read' => $message->is_read,
            ];
        })]);
    }

    public function postMessages(Request $request, $chatName)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $userId = Auth::id();
        $content = trim($request->input('content'));

        $chat = Chat::where('name', $chatName)->first();

        if (!$chat) {
            return response()->json(['error' => 'El chat no existe'], 404);
        }

        if (!$chat->users()->where('user_id', $userId)->exists()) {
            return response()->json(['error' => 'No tienes permiso para enviar mensajes en este chat'], 403);
        }

        $message = Message::create([
            'chat_id' => $chat->id,
            'user_id' => $userId,
            'content' => $content,
            'is_read' => false,
        ]);

        $message->load('user');

        // Enviar evento para WebSockets
        broadcast(new MessageSentEvent($message))->toOthers();

        return response()->json([
            'message' => [
                'id' => $message->id,
                'content' => $message->content,
                'user_id' => $message->user_id,
                'user' => [
                    'id' => $message->user->id,
                    'name' => $message->user->name,
                    'profile_photo_url' => $message->user->profile_photo_url,
                ],
                'created_at' => $message->created_at->toDateTimeString(),
                'is_edited' => $message->is_edited,
                'is_read' => $message->is_read,
            ],
            'messages' => Message::where('chat_id', $chat->id)->orderBy('created_at', 'asc')->get()
        ], 201);
    }

    public function updateMessages(Request $request, Message $message)
    {
        try {
            // Check if the user is authenticated
            if (!Auth::check()) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }

            // Validate the request
            $request->validate([
                'content' => 'required|string|min:1|max:1000',
            ]);

            // Check if the user has permission to edit the message
            if ($message->user_id !== Auth::id()) {
                return response()->json(['error' => 'No tienes permiso para editar este mensaje'], 403);
            }

            // Check if the chat exists
            $chat = $message->chat;
            if (!$chat) {
                return response()->json(['error' => 'El chat asociado al mensaje no existe'], 404);
            }

            // Create a history record for the edit
            MessageHistory::create([
                'message_id' => $message->id,
                'user_id' => Auth::id(),
                'old_content' => $message->content,
                'action' => 'edited',
                'changed_at' => now(),
            ]);

            // Update the message
            $message->update([
                'content' => $request->input('content'),
                'is_edited' => true,
            ]);

            // Broadcast the edit event to all users in the chat
            foreach ($chat->users as $chatUser) {
                event(new MessageEditedEvent($message, $chat));
            }

            // Return a simplified response to avoid serialization issues
            return response()->json([
                'message' => [
                    'id' => $message->id,
                    'content' => $message->content,
                    'user_id' => $message->user_id,
                    'is_edited' => $message->is_edited,
                    'created_at' => $message->created_at->toDateTimeString(),
                ],
                'edited' => true,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Error editing message: ' . $e->getMessage(), [
                'message_id' => $message->id,
                'user_id' => Auth::id(),
                'request_content' => $request->input('content'),
                'exception' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Error interno al editar el mensaje'], 500);
        }
    }

    public function destroy(Message $message)
    {
        MessageHistory::create([
            'message_id' => $message->id,
            'user_id' => Auth::id(),
            'old_content' => $message->content,
            'action' => 'deleted',
            'changed_at' => now(),
        ]);

        $chat = $message->chat;
        $message->delete();

        foreach ($chat->users as $chatUser) {
            event(new MessageDeletedEvent($message->id, $chat));
        }

        return response()->json(['message' => 'Mensaje eliminado']);
    }

    public function markMessageAsRead(Request $request, $chatName, $messageId)
    {
        $chat = Chat::where('name', $chatName)->first();
        if (!$chat) {
            return response()->json(['error' => 'El chat no existe'], 404);
        }

        $message = Message::where('id', $messageId)
            ->where('chat_id', $chat->id)
            ->where('user_id', '!=', Auth::id())
            ->first();

        if (!$message) {
            return response()->json(['error' => 'Mensaje no encontrado o no tienes permiso'], 404);
        }

        $message->update(['is_read' => true]);

        return response()->json(['message' => 'Mensaje marcado como leído']);
    }
}
