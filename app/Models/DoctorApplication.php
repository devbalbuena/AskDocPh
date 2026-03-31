<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DoctorApplication extends Model
{
    protected $fillable = [
        'user_id',
        'status',
        'submitted_at',
        'reviewed_at',
        'reviewed_by_admin_id',
        'admin_notes',
        'biometrics',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'reviewed_at'  => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewedByAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'reviewed_by_admin_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(DoctorApplicationDocument::class);
    }

    public function professionalTitles(): HasMany
    {
        return $this->hasMany(DoctorApplicationProfessionalTitle::class);
    }
}
