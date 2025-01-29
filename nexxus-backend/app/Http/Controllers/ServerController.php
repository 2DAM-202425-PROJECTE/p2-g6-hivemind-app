<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Server;

class ServerController extends Controller
{
    public function index()
    {
        return view('servers.index');
    }

    public function create()
    {
        return view('servers.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name_server' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'owner' => 'required|integer|exists:users,id',
            'creation_date' => 'required|date',
            'photo_server' => 'required|string|max:255',
        ]);

        $server = Server::create($validatedData);

        return response()->json([
            'message' => 'Server created successfully',
            'data' => $server,
        ], 201);
    }
}
