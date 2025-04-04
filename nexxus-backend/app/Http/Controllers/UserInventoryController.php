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
                'item' => [
                    'name' => $inventoryItem->item->name,
                    'price' => $inventoryItem->item->price,
                    'iconUrl' => $inventoryItem->item->iconUrl,
                    'type' => $inventoryItem->item->type ?? (str_contains($inventoryItem->item->name, 'Icon') ? 'profile_icon' : (str_contains($inventoryItem->item->name, 'Frame') ? 'profile_frame' : 'other')),
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

        // Check if the item already exists in the user's inventory
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
}
