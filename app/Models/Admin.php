<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $table = 'admins';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'email',
        'password',
        'fname',
        'mname',
        'lname',
        'gender',
        'bday',
        'avatar_url',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Attribute casts.
     */
    protected function casts(): array
    {
        return [
            'bday'     => 'date',
            'password' => 'hashed',
        ];
    }
}
