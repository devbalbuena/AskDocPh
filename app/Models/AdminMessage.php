<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminMessage extends Model
{
    protected $fillable = [
        'from_admin_id',
        'to_admin_id',
        'body',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    public function fromAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'from_admin_id');
    }

    public function toAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'to_admin_id');
    }
}
