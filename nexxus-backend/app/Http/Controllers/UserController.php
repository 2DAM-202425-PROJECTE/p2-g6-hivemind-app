<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(): \Illuminate\Http\JsonResponse
    {
        $users = User::all();
        return response()->json($users);
    }

    public function show($id): \Illuminate\Http\JsonResponse
    {
        $user = User::find($id);
        return response()->json($user);
    }
}
