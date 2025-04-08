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
        return response()->json(['message' => 'Users retrieved successfully', 'data' => $users], 200);
    }

    public function posts($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $posts = $user->posts()->with(['likes', 'comments.user'])->get();
        $posts->each(function ($post) {
            $post->liked_by_user = $post->likes->contains('user_id', auth()->id());
            $post->likes_count = $post->likes->count();
        });

        return response()->json(['message' => 'User posts retrieved successfully', 'data' => $posts], 200);
    }

    public function getRandomUsers()
    {
        $currentUserId = auth()->id();
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
        try {
            $request->validate([
                'userId' => 'required|integer|exists:users,id',
                'itemId' => 'required|integer|exists:items,id',
            ]);

            $userId = $request->input('userId');
            $itemId = $request->input('itemId');

            $user = User::findOrFail($userId);
            $item = Item::findOrFail($itemId);

            if ($user->credits < $item->price) {
                return response()->json(['error' => 'Not enough credits'], 400);
            }

            $existingItem = UserInventory::where('user_id', $userId)->where('item_id', $itemId)->first();
            if ($existingItem) {
                return response()->json(['error' => 'You already own this item'], 400);
            }

            \DB::transaction(function () use ($user, $item, $userId, $itemId) {
                $user->credits -= $item->price;
                $user->save();

                $inventory = new UserInventory();
                $inventory->user_id = $userId;
                $inventory->item_id = $itemId;
                $inventory->save();
            });

            return response()->json([
                'message' => 'Purchase successful with credits',
                'credits' => $user->credits,
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Purchase failed: ' . $e->getMessage());
            return response()->json(['error' => 'Purchase failed', 'details' => $e->getMessage()], 500);
        }
    }

    public function updateCredits(Request $request)
    {
        try {
            $request->validate([
                'userId' => 'required|integer|exists:users,id',
                'credits' => 'required|integer|min:0',
            ]);

            $user = User::findOrFail($request->input('userId'));
            $user->credits = $request->input('credits');
            $user->save();

            return response()->json(['message' => 'User credits updated successfully', 'credits' => $user->credits], 200);
        } catch (\Exception $e) {
            \Log::error('Failed to update credits: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update credits', 'details' => $e->getMessage()], 500);
        }
    }

    public function searchUsers(Request $request)
    {
        $validatedData = $request->validate([
            'username' => 'required|string|max:32',
        ]);

        $users = User::where('username', 'like', '%' . $validatedData['username'] . '%')->get();
        return response()->json(['message' => 'Users retrieved successfully', 'data' => $users], 200);
    }

    public function getUserByUsername($username)
    {
        $user = User::where('username', $username)->firstOrFail();
        $posts = $user->posts;
        return response()->json(['user' => $this->appendEquippedItems($user), 'posts' => $posts], 200);
    }

    public function updateEquippedProfileIcon(Request $request)
    {
        $user = User::find($request->userId);
        $user->equipped_profile_icon_path = $request->equipped_profile_icon_path;
        $user->save();

        return response()->json(['message' => 'Profile icon updated successfully']);
    }

    public function updateEquippedProfileFrame(Request $request)
    {
        $request->validate([
            'userId' => 'required|integer|exists:users,id',
            'equippedProfileFramePath' => 'nullable|string',
        ]);

        $user = User::find($request->input('userId'));
        $user->equipped_profile_frame_path = $request->input('equippedProfileFramePath');
        $user->save();

        return response()->json(['message' => 'Equipped profile frame updated successfully'], 200);
    }

    public function updateEquippedBackground(Request $request)
    {
        $request->validate([
            'userId' => 'required|exists:users,id',
            'equipped_background_path' => 'nullable|string',
        ]);

        $user = User::find($request->userId);
        $user->equipped_background_path = $request->equipped_background_path;
        $user->save();

        return response()->json(['message' => 'Background updated successfully']);
    }

    public function updateEquippedAnimation(Request $request)
    {
        $request->validate([
            'userId' => 'required|integer|exists:users,id',
            'equippedAnimationPath' => 'nullable|string',
        ]);

        $user = User::find($request->input('userId'));
        $user->equipped_animation_path = $request->input('equippedAnimationPath');
        $user->save();

        return response()->json(['message' => 'Equipped animation updated successfully'], 200);
    }

    public function updateEquippedNameEffect(Request $request)
    {
        $request->validate([
            'userId' => 'required|exists:users,id',
            'equipped_name_effect_path' => 'nullable|string',
        ]);

        $user = User::findOrFail($request->userId);
        $user->equipped_name_effect_path = $request->equipped_name_effect_path;
        $user->save();

        return response()->json(['message' => 'Name effect updated successfully', 'user' => $user]);
    }

    public function updateEquippedBadge(Request $request)
    {
        $request->validate([
            'userId' => 'required|integer|exists:users,id',
            'equippedBadgePath' => 'nullable|string',
        ]);

        $user = User::find($request->input('userId'));
        $user->equipped_badge_path = $request->input('equippedBadgePath');
        $user->save();

        return response()->json(['message' => 'Equipped badge updated successfully'], 200);
    }

    public function getEquippedItems($id)
    {
        $user = User::findOrFail($id);
        return response()->json($this->appendEquippedItems($user), 200);
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
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $profilePhotoPath = $request->file('profile_photo')->store('profile_photos', 'public');
            $user->profile_photo_path = $profilePhotoPath;
        } elseif ($request->has('profile_photo') && $request->input('profile_photo') === null) {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $user->profile_photo_path = null;
        }

        if ($request->hasFile('banner_photo')) {
            if ($user->banner_photo_path) {
                Storage::disk('public')->delete($user->banner_photo_path);
            }
            $bannerPhotoPath = $request->file('banner_photo')->store('banner_photos', 'public');
            $user->banner_photo_path = $bannerPhotoPath;
        } elseif ($request->has('banner_photo') && $request->input('banner_photo') === null) {
            if ($user->banner_photo_path) {
                Storage::disk('public')->delete($user->banner_photo_path);
            }
            $user->banner_photo_path = null;
        }

        $user->name = $validatedData['name'];
        $user->description = $validatedData['description'] ?? null;
        $user->save();

        return response()->json(['message' => 'Profile updated successfully', 'user' => $this->appendEquippedItems($user)], 200);
    }

    private function appendEquippedItems($user)
    {
        $user->equipped_profile_icon_path = $user->equipped_profile_icon_path;
        $user->equipped_profile_frame_path = $user->equipped_profile_frame_path;
        $user->equipped_background_path = $user->equipped_background_path;
        $user->equipped_animation_path = $user->equipped_animation_path;
        $user->equipped_name_effect_path = $user->equipped_name_effect_path;
        $user->equipped_badge_path = $user->equipped_badge_path;

        return $user;
    }
}
