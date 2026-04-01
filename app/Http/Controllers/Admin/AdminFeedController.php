<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\View\View;

class AdminFeedController extends Controller
{
    /** GET /admin/feed — read-only feed for admin */
    public function index(): View
    {
        $posts = Post::with(['user', 'likes', 'comments', 'media'])
            ->latest()
            ->paginate(20);

        return view('admin.feed.index', compact('posts'));
    }
}
