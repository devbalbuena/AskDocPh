<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctorReferral extends Model
{
    protected $fillable = [
        'referring_doctor_id',
        'referred_to_doctor_id',
        'patient_id',
        'appointment_id',
        'reason',
        'message',
        'status',
    ];

    public function referringDoctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referring_doctor_id');
    }

    public function referredToDoctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_to_doctor_id');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }
}
