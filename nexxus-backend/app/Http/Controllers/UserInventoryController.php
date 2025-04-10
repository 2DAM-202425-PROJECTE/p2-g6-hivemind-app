<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserInventory;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class UserInventoryController extends Controller
{
    public function index($id)
    {
        $userId = Auth::id();
        if ($userId != $id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $inventory = UserInventory::where('user_id', $id)->with('item')->get();
        $formattedInventory = $inventory->map(function ($inventoryItem) {
            return [
                'id' => $inventoryItem->id,
                'item_id' => $inventoryItem->item_id, // Added item_id for clarity
                'item' => [
                    'name' => $inventoryItem->item->name,
                    'price' => $inventoryItem->item->price,
                    'iconUrl' => $inventoryItem->item->iconUrl,
                    'type' => $inventoryItem->item->type ?? $this->determineItemType($inventoryItem->item->name),
                    'theme_value' => $inventoryItem->item->theme_value,
                ],
            ];
        });

        return response()->json($formattedInventory, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
        ]);

        $user = Auth::user();
        $item_id = $request->input('item_id');

        $existingItem = UserInventory::where('user_id', $user->id)->where('item_id', $item_id)->first();
        if ($existingItem) {
            return response()->json(['error' => 'Item already exists in inventory'], 400);
        }

        $userInventory = new UserInventory();
        $userInventory->user_id = $user->id;
        $userInventory->item_id = $item_id;
        $userInventory->save();

        return response()->json($userInventory, 201);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $userInventory = UserInventory::where('user_id', $user->id)->where('id', $id)->first();

        if (!$userInventory) {
            return response()->json(['error' => 'Item not found in inventory'], 404);
        }

        $userInventory->delete();

        return response()->json(['message' => 'Item removed from inventory'], 200);
    }

    private function determineItemType($itemName)
    {
        if (str_contains($itemName, 'Credits')) return 'credit_pack';
        if (str_contains($itemName, 'Badge')) return 'badge';
        if (str_contains($itemName, 'Font')) return 'profile_font';
        if (str_contains($itemName, 'Icon')) return 'profile_icon';
        if (in_array($itemName, [
            'Soft Gradient', 'Starry Night', 'Minimal Waves', 'Pastel Sky', 'Urban Glow', 'Forest Mist', 'Ocean Depth',
            'Desert Dunes', 'Mountain Peak', 'Polar Glow', 'Lush Valley', 'Dusk Metropolis', 'Golden Fields', 'Volcanic Ash',
            'Nebula Cloud', 'Twilight Horizon'
        ])) return 'background';
        if (in_array($itemName, [
            'Gradient Fade', 'Golden Outline', 'Dark Pulse', 'Cosmic Shine', 'Neon Edge', 'Frost Glow', 'Fire Flicker',
            'Emerald Sheen', 'Phantom Haze', 'Electric Glow', 'Solar Flare', 'Wave Shimmer', 'Crystal Pulse', 'Mystic Aura',
            'Shadow Veil', 'Digital Pulse'
        ])) return 'name_effect';
        if (in_array($itemName, [
            'Cosmic Vortex', 'Neon Cityscape', 'Firestorm Horizon', 'Mystic Nebula', 'Cyber Grid', 'Ethereal Waves',
            'Ocean Surge', 'Pixel Storm', 'Lava Flow', 'Frost Vortex', 'Steampunk Gears', 'Lunar Eclipse', 'Glitch Matrix',
            'Aurora Dance', 'Galactic Spin', 'Rainbow Flux'
        ])) return 'custom_banner';
        return 'other';
    }
}
