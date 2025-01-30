<?php

namespace App\Http\Controllers;

use App\Models\PrivateMessage;
use Illuminate\Http\Request;

class PrivateMessageController extends Controller
{
    /**
     * Store a newly created private message in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        return PrivateMessage::all();
    }

    public function create()
    {
        return view('private_messages.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'id_sender' => 'required|integer|exists:users,id',
            'id_receiver' => 'required|integer|exists:users,id',
            'content' => 'required|string|max:255',
            'send_date' => 'required|date',
            'read_status' => 'required|boolean',
        ]);


        $privateMessage = PrivateMessage::create($validatedData);

        return response()->json([
            'message' => 'Private message sent successfully',
            'data' => $privateMessage,
        ], 201);
    }
}
