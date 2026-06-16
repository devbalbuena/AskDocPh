<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\BookmarkCollection;
use App\Models\BookmarkCollectionItem;
use App\Models\PostBookmark;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class BookmarkCollectionController extends Controller
{
    /** GET /bookmarks/collections */
    public function index(): View
    {
        $collections = BookmarkCollection::where('user_id', auth()->id())
            ->withCount('items')
            ->get();
            
        // Show all bookmarks if default "All Bookmarks" selected
        $bookmarks = PostBookmark::where('user_id', auth()->id())
            ->with(['post.user', 'post.media', 'post.likes'])
            ->latest()
            ->paginate(15);
            
        return view('patient.bookmarks.index', compact('collections', 'bookmarks'));
    }

    /** POST /bookmarks/collections */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $collection = BookmarkCollection::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json(['success' => true, 'collection' => $collection]);
    }

    /** GET /bookmarks/collections/{collection} */
    public function show(BookmarkCollection $collection): View
    {
        abort_if($collection->user_id !== auth()->id(), 403);

        $collections = BookmarkCollection::where('user_id', auth()->id())
            ->withCount('items')
            ->get();
            
        // Get the bookmarks in this specific collection
        $itemIds = $collection->items()->pluck('post_bookmark_id');
        
        $bookmarks = PostBookmark::whereIn('id', $itemIds)
            ->with(['post.user', 'post.media', 'post.likes'])
            ->latest()
            ->paginate(15);

        return view('patient.bookmarks.index', compact('collections', 'bookmarks', 'collection'));
    }

    /** DELETE /bookmarks/collections/{collection} */
    public function destroy(BookmarkCollection $collection): JsonResponse
    {
        abort_if($collection->user_id !== auth()->id(), 403);
        $collection->delete();
        return response()->json(['success' => true]);
    }

    /** POST /bookmarks/collections/{collection}/add */
    public function addItem(BookmarkCollection $collection, Request $request): JsonResponse
    {
        abort_if($collection->user_id !== auth()->id(), 403);
        
        $request->validate(['post_bookmark_id' => 'required|exists:post_bookmarks,id']);

        $exists = BookmarkCollectionItem::where('collection_id', $collection->id)
            ->where('post_bookmark_id', $request->post_bookmark_id)
            ->exists();

        if (!$exists) {
            BookmarkCollectionItem::create([
                'collection_id' => $collection->id,
                'post_bookmark_id' => $request->post_bookmark_id,
            ]);
        }

        return response()->json(['success' => true]);
    }

    /** DELETE /bookmarks/collections/{collection}/items/{item} */
    public function removeItem(BookmarkCollection $collection, $itemId): JsonResponse
    {
        abort_if($collection->user_id !== auth()->id(), 403);

        $item = BookmarkCollectionItem::where('collection_id', $collection->id)
            ->where('post_bookmark_id', $itemId)
            ->first();
            
        if ($item) {
            $item->delete();
        }

        return response()->json(['success' => true]);
    }
}
