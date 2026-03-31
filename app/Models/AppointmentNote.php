<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppointmentNote extends Model
{
    protected $fillable = [
        'appointment_id',
        'doctor_id',
        'notes',
        'diagnosis',
        'recommendations',
        'is_visible_to_patient',
    ];

    protected function casts(): array
    {
        return [
            'is_visible_to_patient' => 'boolean',
        ];
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
}
