<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostLike;
use App\Models\PostComment;
use App\Models\PostShare;
use Illuminate\View\View;

class PostAnalyticsController extends Controller
{
    /** GET /profile/analytics */
    public function index(): View
    {
        $userId = auth()->id();

        // Only doctors and admins can view analytics
        abort_unless(in_array(auth()->user()->role, ['doctor', 'admin']), 403);

        // Get user's posts with engagement counts
        $posts = Post::where('user_id', $userId)
            ->withCount(['likes', 'comments', 'shares'])
            ->with('media')
            ->whereNull('deleted_at')
            ->latest()
            ->limit(20)
            ->get();

        // Aggregate totals
        $totalLikes    = $posts->sum('likes_count');
        $totalComments = $posts->sum('comments_count');
        $totalShares   = $posts->sum('shares_count');

        // Top 5 posts by engagement
        $topPosts = $posts->map(function ($post) {
            $post->engagement = $post->likes_count + $post->comments_count + $post->shares_count;
            return $post;
        })->sortByDesc('engagement')->take(5)->values();

        // Reaction breakdown for likes (across all user's posts)
        $postIds = Post::where('user_id', $userId)->whereNull('deleted_at')->pluck('id');

        $reactionBreakdown = PostLike::whereIn('post_id', $postIds)
            ->selectRaw('reaction_type, COUNT(*) as count')
            ->groupBy('reaction_type')
            ->get()
            ->pluck('count', 'reaction_type')
            ->toArray();

        // Last 7 days engagement trend
        $trend = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i);
            $dayLikes    = PostLike::whereIn('post_id', $postIds)->whereDate('created_at', $day)->count();
            $dayComments = PostComment::whereIn('post_id', $postIds)->whereDate('created_at', $day)->count();
            $dayShares   = PostShare::whereIn('post_id', $postIds)->whereDate('created_at', $day)->count();
            $trend[] = [
                'date'     => $day->format('M d'),
                'likes'    => $dayLikes,
                'comments' => $dayComments,
                'shares'   => $dayShares,
            ];
        }

        $layout = match (auth()->user()->role) {
            'doctor' => 'layouts.doctor',
            'admin'  => 'layouts.admin',
            default  => 'layouts.patient',
        };

        return view('shared.analytics.index', compact(
            'posts', 'topPosts', 'totalLikes', 'totalComments', 'totalShares',
            'reactionBreakdown', 'trend', 'layout'
        ));
    }
}
