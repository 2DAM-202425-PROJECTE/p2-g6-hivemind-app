<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Channel;

class ChannelController extends Controller
{
    public function index()
    {
        return view('channel.index');
    }
    
    public function create()
    {
        return view('channel.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name_channel' => 'required|string|max:255',
            'type_channel' => 'required|in:text,voice',
            'id_server' => 'required|integer|exists:server,id',
        ]);

        $channel = Channel::create($validatedData);

        return response()->json([
            'message' => 'Channel created successfully',
            'data' => $channel,
        ], 201);
    }
}
