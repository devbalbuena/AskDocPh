<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserFollow;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    /**
     * POST /users/{user}/follow
     * Toggle follow / unfollow. Returns JSON so the view can update without reload.
     */
    public function toggle(User $user): JsonResponse
    {
        $authUser = auth()->user();

        // Prevent self-following
        if ($authUser->id === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot follow yourself.',
            ], 422);
        }

        $existing = UserFollow::where('follower_id', $authUser->id)
            ->where('following_id', $user->id)
            ->first();

        if ($existing) {
            // Already following — unfollow
            $existing->delete();
            $following = false;
        } else {
            // Not yet following — follow
            UserFollow::create([
                'follower_id'  => $authUser->id,
                'following_id' => $user->id,
            ]);
            $following = true;
        }

        return response()->json([
            'success'         => true,
            'following'       => $following,
            'followers_count' => $user->fresh()->followersCount(),
        ]);
    }

    /**
     * GET /users/{user}/followers
     * List everyone who follows the given user.
     */
    public function followers(User $user): JsonResponse
    {
        $followers = $user->followers()
            ->with('follower:id,fname,lname,username,profile_photo,role')
            ->latest('created_at')
            ->paginate(20);

        return response()->json([
            'data'  => $followers->map(fn($f) => [
                'id'            => $f->follower->id,
                'display_name'  => $f->follower->display_name,
                'username'      => $f->follower->username,
                'role'          => $f->follower->role,
                'profile_url'   => url('/users/' . $f->follower->username),
            ]),
            'total' => $followers->total(),
        ]);
    }

    /**
     * GET /users/{user}/following
     * List everyone the given user is following.
     */
    public function following(User $user): JsonResponse
    {
        $following = $user->following()
            ->with('following:id,fname,lname,username,profile_photo,role')
            ->latest('created_at')
            ->paginate(20);

        return response()->json([
            'data'  => $following->map(fn($f) => [
                'id'            => $f->following->id,
                'display_name'  => $f->following->display_name,
                'username'      => $f->following->username,
                'role'          => $f->following->role,
                'profile_url'   => url('/users/' . $f->following->username),
            ]),
            'total' => $following->total(),
        ]);
    }
}
