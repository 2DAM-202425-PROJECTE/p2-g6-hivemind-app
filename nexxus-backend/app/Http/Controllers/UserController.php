<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

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

        if ($request->hasFile('profile_photo')) {
            $profilePhotoPath = $request->file('profile_photo')->store('profile_photos', 'public');
            $user->profile_photo_path = $profilePhotoPath;
        }

        if ($request->hasFile('banner_photo')) {
            $bannerPhotoPath = $request->file('banner_photo')->store('banner_photos', 'public');
            $user->banner_photo_path = $bannerPhotoPath;
        }

        $user->name = $validatedData['name'];
        $user->description = $validatedData['description'];

        $user->save();

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user,
        ], 200);
    }
}
