<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostBookmark;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class BookmarkController extends Controller
{
    /** GET /patient/bookmarks — list all bookmarked posts */
    public function index(): View
    {
        $bookmarks = PostBookmark::where('user_id', auth()->id())
            ->with(['post.user', 'post.media', 'post.likes'])
            ->latest()
            ->paginate(15);

        $collections = \App\Models\BookmarkCollection::where('user_id', auth()->id())
            ->withCount('items')
            ->get();

        return view('patient.bookmarks.index', compact('bookmarks', 'collections'));
    }

    /** POST /patient/bookmarks/{post}/toggle — add or remove bookmark (Axios JSON) */
    public function toggle(Post $post): JsonResponse
    {
        $userId = auth()->id();

        $existing = PostBookmark::where('user_id', $userId)
            ->where('post_id', $post->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $bookmarked = false;
        } else {
            PostBookmark::create([
                'user_id' => $userId,
                'post_id' => $post->id,
            ]);
            $bookmarked = true;
        }

        return response()->json([
            'bookmarked' => $bookmarked,
        ]);
    }
}
