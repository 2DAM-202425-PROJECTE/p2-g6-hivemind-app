<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    //return all users
    public function index()
    {
        $users = User::all();

        return response()->json([
            'message' => 'Users retrieved successfully',
            'data' => $users,
        ], 200);
    }


    public function getRandomUsers()
    {
        $currentUserId = auth()->id(); // Get the currently signed-in user's ID
        $users = User::where('id', '!=', $currentUserId)->inRandomOrder()->limit(4)->get();

        $blankUsers = [];
        for ($i = $users->count(); $i < 4; $i++) {
            $blankUsers[] = [
                'id' => null,
                'name' => 'Blank User',
                'profile_photo_url' => 'path/to/default/profile/photo.png',
            ];
        }

        $users = $users->merge($blankUsers);

        return response()->json($users);
    }

    // search users by username
    public function searchUsers(Request $request)
    {
        $validatedData = $request->validate([
            'username' => 'required|string|max:32',
        ]);

        $users = User::where('username', 'like', '%' . $validatedData['username'] . '%')->get();

        return response()->json([
            'message' => 'Users retrieved successfully',
            'data' => $users,
        ], 200);
    }


    public function getUserByUsername($username)
    {
        $user = User::where('username', $username)->firstOrFail();
        $posts = $user->posts;

        return response()->json([
            'user' => $user,
            'posts' => $posts,
        ], 200);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validatedData = $request->validate([
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:20480',
            'banner_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:20480',
            'name' => 'required|string|max:32',
            'description' => 'nullable|string|max:150',
        ]);

        // Manejar la foto de perfil
        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $profilePhotoPath = $request->file('profile_photo')->store('profile_photos', 'public');
            $user->profile_photo_path = $profilePhotoPath;
        } elseif ($request->has('profile_photo') && $request->input('profile_photo') === null) {
            // Si se envía profile_photo como null explícitamente, eliminar la imagen
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $user->profile_photo_path = null;
        }

        // Manejar el banner
        if ($request->hasFile('banner_photo')) {
            if ($user->banner_photo_path) {
                Storage::disk('public')->delete($user->banner_photo_path);
            }
            $bannerPhotoPath = $request->file('banner_photo')->store('banner_photos', 'public');
            $user->banner_photo_path = $bannerPhotoPath;
        } elseif ($request->has('banner_photo') && $request->input('banner_photo') === null) {
            // Si se envía banner_photo como null explícitamente, eliminar la imagen
            if ($user->banner_photo_path) {
                Storage::disk('public')->delete($user->banner_photo_path);
            }
            $user->banner_photo_path = null;
        }

        // Actualizar nombre y descripción
        $user->name = $validatedData['name'];
        $user->description = $validatedData['description'] ?? null;

        $user->save();

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user,
        ], 200);
    }
}
