<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompleteProfileRequest;
use App\Models\Item;
use App\Models\UserInventory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
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

            // Prevent purchasing credit packs with credits
            if ($item->type === 'credit_pack') {
                return response()->json(['error' => 'Cannot purchase credit packs with credits'], 400);
            }

            // Check for sufficient credits
            if ($user->credits < $item->price) {
                return response()->json(['error' => 'Not enough credits'], 400);
            }

            // Check if item is already owned
            $existingItem = UserInventory::where('user_id', $userId)->where('item_id', $itemId)->first();
            if ($existingItem) {
                return response()->json(['error' => 'You already own this item'], 400);
            }

            // Process purchase in a transaction
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
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Invalid input', 'details' => $e->errors()], 422);
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
                'amount' => 'required|integer|min:1',
            ]);

            $user = User::findOrFail($request->input('userId'));
            $amount = $request->input('amount');

            \DB::transaction(function () use ($user, $amount) {
                $user->credits += $amount;
                $user->save();
            });

            return response()->json([
                'message' => 'User credits updated successfully',
                'credits' => $user->credits,
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Invalid input', 'details' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Failed to update credits: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update credits', 'details' => $e->getMessage()], 500);
        }
    }

    public function processRealMoneyPurchase(Request $request)
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

            // Check if item is already owned
            $existingItem = UserInventory::where('user_id', $userId)->where('item_id', $itemId)->first();
            if ($existingItem) {
                return response()->json(['error' => 'You already own this item'], 400);
            }

            // Process the purchase in a transaction
            \DB::transaction(function () use ($user, $item, $userId, $itemId) {
                if ($item->type === 'credit_pack' && $item->amount) {
                    // Handle credit pack purchase
                    $user->credits += $item->amount;
                    $user->save();
                } else {
                    // Handle other item types by adding to inventory
                    $inventory = new UserInventory();
                    $inventory->user_id = $userId;
                    $inventory->item_id = $itemId;
                    $inventory->save();
                }
            });

            return response()->json([
                'message' => $item->type === 'credit_pack' ? 'Credits purchased successfully' : 'Item purchased successfully',
                'credits' => $user->credits,
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Invalid input', 'details' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Purchase failed: ' . $e->getMessage());
            return response()->json(['error' => 'Purchase failed', 'details' => $e->getMessage()], 500);
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

    public function updateSubscription(Request $request)
    {
        $request->validate([
            'userId' => 'required|integer|exists:users,id',
            'subscriptionTier' => 'required|string',
        ]);

        $user = User::findOrFail($request->input('userId'));
        $currentTier = $user->subscription_tier;
        $newTier = $request->input('subscriptionTier');

        // Determine if it's an upgrade or downgrade
        $tiers = ['basic', 'standard', 'premium']; // Example tier hierarchy
        $isUpgrade = array_search($newTier, $tiers) > array_search($currentTier, $tiers);

        $user->subscription_tier = $newTier;
        $user->save();

        return response()->json([
            'message' => $isUpgrade ? 'Subscription upgraded successfully' : 'Subscription downgraded successfully',
            'action' => $isUpgrade ? 'Upgrade' : 'Downgrade',
        ], 200);
    }
    public function updateEquippedProfileIcon(Request $request)
    {
        $request->validate([
            'userId' => 'required|integer|exists:users,id',
            'equipped_profile_icon_path' => 'nullable|string',
        ]);
        $user = User::find($request->input('userId'));
        $user->equipped_profile_icon_path = $request->input('equipped_profile_icon_path');
        $user->save();
        return response()->json(['message' => 'Profile icon updated successfully'], 200);
    }

    public function updateProfileIconColor(Request $request)
    {
        $request->validate([
            'userId' => 'required|integer|exists:users,id',
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        $user = User::findOrFail($request->input('userId'));
        $user->equipped_profile_icon_color = $request->input('color');
        $user->save();

        return response()->json(['message' => 'Profile icon color updated successfully', 'color' => $user->equipped_profile_icon_color], 200);
    }

    public function updateEquippedCustomBanner(Request $request)
    {
        $request->validate([
            'userId' => 'required|integer|exists:users,id',
            'equipped_custom_banner' => 'nullable|string', // Accept this for compatibility
        ]);

        $user = User::find($request->input('userId'));
        // Use equipped_banner_photo_path instead
        $user->equipped_banner_photo_path = $request->input('equipped_custom_banner');
        $user->save();

        return response()->json(['message' => 'Custom banner updated successfully'], 200);
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

    public function updateEquippedProfileFont(Request $request)
    {
        try {
            $request->validate([
                'userId' => 'required|integer|exists:users,id',
                'item_id' => 'nullable|integer|exists:items,id',
            ]);

            $user = User::findOrFail($request->input('userId'));
            $itemId = $request->input('item_id');

            if (!$itemId) {
                $user->equipped_profile_font_path = null;
            } else {
                $item = Item::findOrFail($itemId);
                if ($item->type !== 'profile_font') {
                    return response()->json(['message' => 'Invalid item type'], 400);
                }
                $user->equipped_profile_font_path = $item->name;
            }

            $user->save();

            return response()->json(['message' => 'Profile font updated successfully', 'user' => $user], 200);
        } catch (\Exception $e) {
            \Log::error('Failed to update profile font: ' . $e->getMessage());
            return response()->json(['message' => 'Server Error', 'error' => $e->getMessage()], 500);
        }
    }

    public function updateEquippedNameEffect(Request $request)
    {
        try {
            $request->validate([
                'userId' => 'required|integer|exists:users,id',
                'item_id' => 'nullable|integer|exists:items,id',
            ]);

            $user = User::findOrFail($request->input('userId'));
            $itemId = $request->input('item_id');

            if (!$itemId) {
                $user->equipped_name_effect_path = null;
            } else {
                $item = Item::findOrFail($itemId);
                if ($item->type !== 'name_effect') {
                    return response()->json(['message' => 'Invalid item type'], 400);
                }
                $user->equipped_name_effect_path = $item->name;
            }

            $user->save();

            return response()->json(['message' => 'Name effect updated successfully', 'user' => $user], 200);
        } catch (\Exception $e) {
            \Log::error('Failed to update name effect: ' . $e->getMessage());
            return response()->json(['message' => 'Server Error', 'error' => $e->getMessage()], 500);
        }
    }

    public function updateEquippedBanner(Request $request)
    {
        $request->validate([
            'userId' => 'required|exists:users,id',
            'equipped_banner_photo_path' => 'nullable|string',
        ]);

        $user = User::find($request->userId);
        $newBannerPath = $request->equipped_banner_photo_path;

        \Log::info('Updating equipped banner for user ' . $user->id . '. Old value: ' . ($user->equipped_banner_photo_path ?? 'null') . ', New value: ' . ($newBannerPath ?? 'null'));

        $user->equipped_banner_photo_path = $newBannerPath;
        $saved = $user->save();

        if (!$saved) {
            \Log::error('Failed to update equipped banner for user ' . $user->id);
            return response()->json(['error' => 'Failed to update banner'], 500);
        }

        \Log::info('Equipped banner updated successfully for user ' . $user->id . '. New value: ' . ($user->equipped_banner_photo_path ?? 'null'));
        return response()->json(['message' => 'Banner updated successfully']);
    }

    public function updateEquippedBadge(Request $request)
    {
        try {
            $request->validate([
                'userId' => 'required|integer|exists:users,id',
                'equipped_badge_path' => 'nullable|string',
            ]);

            $user = User::findOrFail($request->input('userId'));
            $badgePath = $request->input('equipped_badge_path');
            \Log::info('Updating equipped badge for user ' . $user->id . ' with path: ' . ($badgePath ?? 'null'));

            \DB::beginTransaction();
            $user->equipped_badge_path = $badgePath;
            $saved = $user->save();
            \DB::commit();

            if (!$saved) {
                \Log::error('Failed to save equipped badge for user ' . $user->id . ': Save operation returned false');
                return response()->json(['error' => 'Failed to save badge'], 500);
            }

            // Refresh the model to confirm the update
            $user->refresh();
            \Log::info('Equipped badge updated successfully for user ' . $user->id . '. New equipped_badge_path: ' . ($user->equipped_badge_path ?? 'null'));

            return response()->json([
                'message' => 'Equipped badge updated successfully',
                'equipped_badge_path' => $user->equipped_badge_path,
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed for update equipped badge: ' . json_encode($e->errors()));
            return response()->json(['error' => 'Invalid input', 'details' => $e->errors()], 422);
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Failed to update equipped badge for user ' . $request->input('userId') . ': ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update badge', 'details' => $e->getMessage()], 500);
        }
    }

    public function getEquippedItems($id)
    {
        $user = User::findOrFail($id);
        \Log::info('Fetching equipped items for user ' . $id . '. Equipped badge path: ' . ($user->equipped_badge_path ?? 'null'));
        return response()->json($this->appendEquippedItems($user), 200);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $validatedData = $request->validate([
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:20480',
            'banner_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:20480',
            'name' => 'required|string|max:32',
            'description' => 'nullable|string|max:150',
            'remove_profile_photo' => 'nullable|string',
            'remove_banner_photo' => 'nullable|string',
        ]);

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $profilePhotoPath = $request->file('profile_photo')->store('profile_photos', 'public');
            $user->profile_photo_path = $profilePhotoPath;
        } elseif ($request->input('remove_profile_photo') === '1') {
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
        } elseif ($request->input('remove_banner_photo') === '1') {
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

    public function completeProfile(CompleteProfileRequest $request): JsonResponse
    {
        $user = Auth::user();
        $data = $request->validated();

        // Si se envía skip=true, solo marcar is_profile_completed
        if (isset($data['skip']) && $data['skip'] === true) {
            $user->is_profile_completed = true;
            $user->save();

            return response()->json([
                'user' => $user,
                'message' => 'Profile completion skipped successfully',
            ], 200);
        }

        // Caso normal: actualizar datos del perfil
        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profile_photos', 'public');
            $data['profile_photo_path'] = $path;
        }

        if ($request->hasFile('banner_photo')) {
            $path = $request->file('banner_photo')->store('banner_photos', 'public');
            $data['banner_photo_path'] = $path;
        }

        // Actualizar solo los campos proporcionados
        $user->update(array_filter($data, fn($value) => !is_null($value) && $value !== ''));

        $user->is_profile_completed = true;
        $user->save();

        return response()->json([
            'user' => $user,
            'message' => 'Profile completed successfully',
        ], 200);
    }

    private function appendEquippedItems($user)
    {
        $user->equipped_profile_icon_path = $user->equipped_profile_icon_path;
        $user->equipped_banner_photo_path = $user->equipped_banner_photo_path;
        $user->equipped_background_path = $user->equipped_background_path;
        $user->equipped_name_effect_path = $user->equipped_name_effect_path;
        $user->equipped_badge_path = $user->equipped_badge_path;
        $user->equipped_profile_font_path = $user->equipped_profile_font_path;

        return $user;
    }
}
