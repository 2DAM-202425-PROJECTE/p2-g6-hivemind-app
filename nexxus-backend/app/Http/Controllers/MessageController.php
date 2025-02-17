<?php

namespace App\Http\Controllers;

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

        broadcast(new MessageSentEvent($message))->toOthers();

        return response()->json([
            'message' => $message,
            'messages' => Message::where('chat_id', $chat->id)->orderBy('created_at', 'asc')->get()
        ], 201);
    }

//    public function update(Request $request, Message $message)
//    {
//        $this->authorize('update', $message);
//
//        // Guardar el historial antes de modificar el mensaje
//        MessageHistory::create([
//            'message_id' => $message->id,
//            'content' => $message->content,
//            'edited_at' => now(),
//        ]);
//
//        $message->update([
//            'content' => $request->content,
//            'is_edited' => true,
//        ]);
//
//        return response()->json($message);
//    }
//
//    public function destroy(Message $message)
//    {
//        $this->authorize('delete', $message);
//
//        $message->delete();
//
//        return response()->json(['message' => 'Mensaje eliminado']);
//    }
}
