<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class FollowController extends Controller
{
    public function follow(Request $request, User $user): JsonResponse
    {
        $follower = Auth::user();
        if ($follower->id === $user->id) {
            return response()->json(['message' => 'You cannot follow yourself'], 422);
        }
        if ($follower->isFollowing($user)) {
            return response()->json(['message' => 'You are already following this user'], 422);
        }
        DB::transaction(function () use ($follower, $user) {
            $follower->following()->attach($user->id);
            $user->increment('followers_count');
            $follower->increment('following_count');
        });
        return response()->json([
            'message' => 'Successfully followed user',
            'user' => $user->only('id', 'username', 'followers_count'),
        ], 200);
    }

    public function unfollow(Request $request, User $user): JsonResponse
    {
        $follower = Auth::user();
        if (!$follower->isFollowing($user)) {
            return response()->json(['message' => 'You are not following this user'], 422);
        }
        DB::transaction(function () use ($follower, $user) {
            $follower->following()->detach($user->id);
            $user->decrement('followers_count');
            $follower->decrement('following_count');
        });
        return response()->json([
            'message' => 'Successfully unfollowed user',
            'user' => $user->only('id', 'username', 'followers_count'),
        ], 200);
    }

    public function followers(User $user, Request $request): JsonResponse
    {
        $query = $request->input('query', '');
        $followers = $user->followers()
            ->when($query, function ($q) use ($query) {
                $q->where('username', 'like', "%{$query}%")
                    ->orWhere('name', 'like', "%{$query}%");
            })
            ->select('users.id', 'users.username', 'users.name', 'users.profile_photo_path')
            ->paginate(20);

        return response()->json([
            'followers' => $followers,
            'followers_count' => $user->followers_count,
        ], 200);
    }

    public function following(User $user, Request $request): JsonResponse
    {
        $query = $request->input('query', '');
        $following = $user->following()
            ->when($query, function ($q) use ($query) {
                $q->where('username', 'like', "%{$query}%")
                    ->orWhere('name', 'like', "%{$query}%");
            })
            ->select('users.id', 'users.username', 'users.name', 'users.profile_photo_path')
            ->paginate(20);

        return response()->json([
            'following' => $following,
            'following_count' => $user->following_count,
        ], 200);
    }

    public function isFollowing(Request $request, User $user): JsonResponse
    {
        $follower = Auth::user();
        $isFollowing = $follower->isFollowing($user);
        return response()->json(['is_following' => $isFollowing], 200);
    }
}
