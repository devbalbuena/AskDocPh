<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostBookmark;
use App\Models\PostLike;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeedController extends Controller
{
    /** GET /feed — paginated posts for the feed */
    public function index(): View
    {
        $posts = Post::with([
            'user',
            'media',
            'likes',
            'comments.user',
            'moodTags.moodTag',
        ])
        ->latest()
        ->paginate(15);

        return view('shared.feed.index', compact('posts'));
    }

    /** POST /posts — create a new post (returns JSON for Axios) */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'text_content' => ['nullable', 'string', 'max:5000'],
            'link_url'     => ['nullable', 'url', 'max:2048'],
            'post_type'    => ['required', 'string', 'in:text,media,link,resource'],
            'media'        => ['nullable', 'array'],
            'media.*'      => ['file', 'mimes:jpg,jpeg,png,gif,mp4,webm', 'max:51200'],
        ]);

        $post = Post::create([
            'user_id'      => auth()->id(),
            'post_type'    => $request->post_type ?? 'text',
            'text_content' => $request->text_content,
            'link_url'     => $request->link_url,
        ]);

        // Handle uploaded media
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $index => $file) {
                $path = $file->store("posts/{$post->id}", 'public');
                $post->media()->create([
                    'media_type' => str_starts_with($file->getMimeType(), 'video') ? 'video' : 'image',
                    'path'       => $path,
                    'mime_type'  => $file->getMimeType(),
                    'size_bytes' => $file->getSize(),
                    'sort_order' => $index,
                ]);
            }
        }

        $post->load(['user', 'media', 'likes', 'moodTags.moodTag']);

        return response()->json([
            'success' => true,
            'post'    => $post,
        ]);
    }

    /** DELETE /posts/{post} — soft delete own post */
    public function destroy(Post $post): JsonResponse
    {
        abort_unless(auth()->id() === $post->user_id, 403);

        $post->delete();

        return response()->json(['success' => true]);
    }

    /** POST /posts/{post}/like — toggle like (Axios) */
    public function toggleLike(Post $post): JsonResponse
    {
        $userId = auth()->id();
        $existing = $post->likes()->where('user_id', $userId)->first();

        if ($existing) {
            $existing->delete();
            $liked = false;
        } else {
            $post->likes()->create(['user_id' => $userId, 'reaction_type' => 'like']);
            $liked = true;
        }

        return response()->json([
            'liked'      => $liked,
            'likes_count' => $post->likes()->count(),
        ]);
    }
}
