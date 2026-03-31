<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyAffirmation extends Model
{
    protected $fillable = [
        'quote',
        'author',
        'is_published',
        'publish_at',
        'created_by_admin_id',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'publish_at'   => 'datetime',
        ];
    }

    public function createdByAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by_admin_id');
    }
}
