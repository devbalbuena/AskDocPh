<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostBookmark;
use App\Models\PostComment;
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
        $posts = Post::with(['user', 'likes', 'comments', 'media', 'moodTags.moodTag'])
            ->latest()
            ->paginate(10);

        $layout = match(auth()->user()->role) {
            'doctor' => 'layouts.doctor',
            'admin'  => 'layouts.admin',
            default  => 'layouts.patient',
        };

        $userBookmarks = auth()->user()->bookmarks()->pluck('post_id')->toArray();

        return view('shared.feed.index', compact('posts', 'layout', 'userBookmarks'));
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

        $textContent = $request->text_content;
        if ($request->input('is_anonymous') == '1' && !empty($textContent)) {
            $textContent = '[ANONYMOUS]' . $textContent;
        }

        $post = Post::create([
            'user_id'      => auth()->id(),
            'post_type'    => $request->post_type ?? 'text',
            'text_content' => $textContent,
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
            'post'    => [
                'id' => $post->id,
                'media' => $post->media->map(function($m) {
                    return [
                        'url' => \Illuminate\Support\Facades\Storage::url($m->path),
                        'type' => $m->media_type,
                    ];
                }),
            ],
        ]);
    }

    /** POST /posts/{post}/comments — add a comment (Axios) */
    public function comment(Request $request, Post $post): JsonResponse
    {
        $request->validate([
            'comment_text' => ['required', 'string', 'max:500'],
        ]);

        $comment = PostComment::create([
            'post_id'           => $post->id,
            'user_id'           => auth()->id(),
            'comment_text'      => $request->comment_text,
            'parent_comment_id' => null,
        ]);

        return response()->json([
            'success' => true,
            'comment' => [
                'id'         => $comment->id,
                'text'       => $comment->comment_text,
                'author'     => auth()->user()->fname . ' ' . auth()->user()->lname,
                'username'   => auth()->user()->username,
                'created_at' => $comment->created_at->diffForHumans(),
            ],
        ]);
    }

    /** DELETE /posts/{post} — soft delete own post */
    public function destroy(Post $post): JsonResponse
    {
        abort_unless(auth()->id() === $post->user_id, 403);

        $post->delete();

        return response()->json(['success' => true]);
    }

    /** POST /posts/{post}/like — toggle/change reaction (Axios) */
    public function toggleLike(Request $request, Post $post): JsonResponse
    {
        $userId       = auth()->id();
        $reactionType = $request->input('reaction_type', 'heart');
        $existing     = $post->likes()->where('user_id', $userId)->first();

        if ($existing) {
            if ($existing->reaction_type === $reactionType) {
                // Same reaction — remove it (toggle off)
                $existing->delete();
                $liked        = false;
                $reactionType = null;
            } else {
                // Different reaction — update it
                $existing->update(['reaction_type' => $reactionType]);
                $liked = true;
            }
        } else {
            $post->likes()->create(['user_id' => $userId, 'reaction_type' => $reactionType]);
            $liked = true;
        }

        return response()->json([
            'liked'         => $liked,
            'likes_count'   => $post->likes()->count(),
            'reaction_type' => $reactionType,
        ]);
    }

    /** POST /posts/{post}/report — log a report (Axios) */
    public function report(Request $request, Post $post): JsonResponse
    {
        $request->validate([
            'reason'  => ['required', 'string', 'in:spam,inappropriate,misinformation,harassment,other'],
            'details' => ['nullable', 'string', 'max:1000'],
        ]);

        \Log::info('Post reported', [
            'post_id'     => $post->id,
            'reported_by' => auth()->id(),
            'reason'      => $request->reason,
            'details'     => $request->details,
        ]);

        return response()->json(['success' => true]);
    }
}
