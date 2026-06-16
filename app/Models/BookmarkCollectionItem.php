<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookmarkCollectionItem extends Model
{
    public $timestamps = false; // We only have created_at handled manually or natively

    protected $fillable = [
        'collection_id',
        'post_bookmark_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function collection(): BelongsTo
    {
        return $this->belongsTo(BookmarkCollection::class, 'collection_id');
    }

    public function postBookmark(): BelongsTo
    {
        return $this->belongsTo(PostBookmark::class, 'post_bookmark_id');
    }
}
