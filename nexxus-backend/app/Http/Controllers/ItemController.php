<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ItemController extends Controller
{
    public function index()
    {
        try {
            $items = Item::all();
            return response()->json($items);
        } catch (\Exception $e) {
            Log::error('Failed to fetch items: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch items'], 500);
        }
    }

    public function show($id)
    {
        try {
            $item = Item::findOrFail($id);
            return response()->json($item);
        } catch (\Exception $e) {
            Log::error('Failed to fetch item: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch item'], 500);
        }
    }

    public function categorizedItems()
    {
        try {
            $subscriptions = Item::whereIn('id', [1, 2, 3])->get();
            $creditPacks = Item::whereIn('id', [4, 5, 6, 7, 8, 9, 10, 11])->get();
            $cosmetics = Item::whereNotIn('id', [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11])->get();

            return response()->json([
                'subscriptions' => $subscriptions,
                'creditPacks' => $creditPacks,
                'cosmetics' => $cosmetics,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch categorized items: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch categorized items'], 500);
        }
    }
}
