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

        return response()->json(['messages' => $messages]);
    }

    public function postMessages(Request $request, $chatName)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $userId = Auth::id();
        $content = trim($request->input('content'));

        // Buscar el chat per nom
        $chat = Chat::where('name', $chatName)->first();

        if (!$chat) {
            return response()->json(['error' => 'El chat no existeix'], 404);
        }

        // Comprovar que l'usuari forma part del xat
        if (!$chat->users()->where('user_id', $userId)->exists()) {
            return response()->json(['error' => 'No tens permís per enviar missatges en aquest chat'], 403);
        }

        // Crear el missatge
        $message = Message::create([
            'chat_id' => $chat->id,
            'user_id' => $userId,
            'content' => $content,
        ]);

        // Enviar evento para WebSockets
        broadcast(new MessageSentEvent($message))->toOthers();

        return response()->json([
            'message' => $message,
            'messages' => Message::where('chat_id', $chat->id)->orderBy('created_at', 'asc')->get()
        ], 201);
    }

    public function updateMessages(Request $request, Message $message)
    {
        $request->validate([
            'content' => 'required|string|min:1',
        ]);

        // Guardar en historial antes de modificar
        MessageHistory::create([
            'message_id' => $message->id,
            'user_id' => Auth::id(),
            'old_content' => $message->content,
            'action' => 'edited',
            'changed_at' => now(),
        ]);

        // Actualizar mensaje
        $message->update([
            'content' => $request->input('content'),
            'is_edited' => true,
        ]);

        // Enviar evento para WebSockets
        broadcast(new MessageEditedEvent($message))->toOthers();

        return response()->json([
            'message' => $message,
            'edited' => true,
        ]);
    }


    public function destroy(Message $message)
    {
        // Guardar en historial antes de eliminar
        MessageHistory::create([
            'message_id' => $message->id,
            'user_id' => Auth::id(),
            'old_content' => $message->content,
            'action' => 'deleted',
            'changed_at' => now(),
        ]);

        $message->delete();

        // Emitir evento de eliminación de mensaje
        broadcast(new MessageDeletedEvent($message->id, $message->chat->name))->toOthers();

        return response()->json(['message' => 'Mensaje eliminado']);
    }
}
