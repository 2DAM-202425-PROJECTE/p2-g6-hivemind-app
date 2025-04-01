<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\UserInventory;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();

        return response()->json([
            'message' => 'Users retrieved successfully',
            'data' => $users,
        ], 200);
    }
    public function posts($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        $posts = $user->posts()->with(['likes', 'comments.user'])->get();

        // Add liked_by_user and likes_count to each post, similar to your show method
        $posts->each(function ($post) {
            $post->liked_by_user = $post->likes->contains('user_id', auth()->id());
            $post->likes_count = $post->likes->count();
        });

        return response()->json([
            'message' => 'User posts retrieved successfully',
            'data' => $posts,
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
    public function processCreditPurchase(Request $request)
    {
        $request->validate([
            'userId' => 'required|integer|exists:users,id',
            'itemId' => 'required|integer|exists:items,id',
        ]);

        $userId = $request->input('userId');
        $itemId = $request->input('itemId');

        $user = User::find($userId);
        $item = Item::find($itemId);

        if ($user->credits >= $item->price) {
            $user->credits -= $item->price;
            $user->save();

            $inventory = new UserInventory();
            $inventory->user_id = $userId;
            $inventory->item_id = $itemId;
            $inventory->save();

            return response()->json(['message' => 'Purchase successful with credits'], 200);
        } else {
            return response()->json(['error' => 'Not enough credits'], 400);
        }
    }

    public function updateCredits(Request $request)
    {
        $user = User::find($request->input('userId'));
        $user->credits = $request->input('credits');
        $user->save();

        return response()->json(['message' => 'User credits updated successfully'], 200);
    }

    public function saveToInventory(Request $request)
    {
        try {
            // Validate the request data
            $validated = $request->validate([
                'userId' => 'required|integer|exists:users,id',
                'itemId' => 'required|integer|exists:items,id',
            ]);

            $userId = $validated['userId'];
            $itemId = $validated['itemId'];

            // Retrieve the user and item
            $user = User::find($userId);
            $item = Item::find($itemId);

            // Check if the user has enough credits
            if ($user->credits < $item->price) {
                return response()->json(['error' => 'Not enough credits'], 400);
            }

            // Deduct the item's price from the user's credits
            $user->credits -= $item->price;
            $user->save();

            // Save the item to the user's inventory
            $inventory = new UserInventory();
            $inventory->user_id = $userId;
            $inventory->item_id = $itemId;
            $inventory->save();

            return response()->json(['message' => 'Item saved to inventory and credits updated'], 201); // 201 for created
        } catch (\Exception $e) {
            \Log::error('Failed to save item to inventory: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to save item to inventory', 'details' => $e->getMessage()], 500);
        }
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

    public function updateEquippedProfileIcon(Request $request)
    {
        $request->validate([
            'userId' => 'required|integer|exists:users,id',
            'equippedProfileIconPath' => 'nullable|string',
        ]);

        $user = User::find($request->input('userId'));
        $user->equipped_profile_icon_path = $request->input('equippedProfileIconPath');
        $user->save();

        return response()->json(['message' => 'Equipped profile icon updated successfully'], 200);
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
