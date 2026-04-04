<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'password',
        'username',
        'fname',
        'mname',
        'lname',
        'gender',
        'bday',
        'role',
        'doctor_status',
        'profile_photo',
        'bio',
        'cover_photo',
        'online_status',
        'allow_ai_recommendation',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at'       => 'datetime',
            'bday'                    => 'date',
            'allow_ai_recommendation' => 'boolean',
            'password'                => 'hashed',
        ];
    }

    // ─── Computed helpers ─────────────────────────────

    public function isDemo(): bool
    {
        return in_array($this->email, [
            'admin@askdocph.com',
            'doctor@askdocph.com',
            'patient@askdocph.com',
        ]);
    }

    public function isVerifiedDoctor(): bool
    {
        return $this->role === 'doctor'
            && $this->doctor_status === 'approved';
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->fname} {$this->mname} {$this->lname}");
    }

    public function getDisplayNameAttribute(): string
    {
        return trim("{$this->fname} {$this->lname}");
    }

    // ─── Social / Feed ────────────────────────────────

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /** Users this user is following */
    public function following(): HasMany
    {
        return $this->hasMany(UserFollow::class, 'follower_id');
    }

    /** Users following this user */
    public function followers(): HasMany
    {
        return $this->hasMany(UserFollow::class, 'following_id');
    }

    /** Check whether the currently authenticated user is following this user */
    public function isFollowing(User $user): bool
    {
        return $this->following()
            ->where('following_id', $user->id)
            ->exists();
    }

    /** Total number of followers this user has */
    public function followersCount(): int
    {
        return $this->followers()->count();
    }

    /** Total number of users this user is following */
    public function followingCount(): int
    {
        return $this->following()->count();
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(PostBookmark::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    // ─── Groups ───────────────────────────────────────

    public function createdGroups(): HasMany
    {
        return $this->hasMany(Group::class, 'creator_id');
    }

    public function groupMemberships(): HasMany
    {
        return $this->hasMany(GroupMember::class);
    }

    // ─── Messaging ────────────────────────────────────

    public function conversationParticipants(): HasMany
    {
        return $this->hasMany(ConversationParticipant::class);
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_user_id');
    }

    // ─── Doctor Application ───────────────────────────

    public function doctorApplications(): HasMany
    {
        return $this->hasMany(DoctorApplication::class);
    }

    // ─── Resources ────────────────────────────────────

    public function resources(): HasMany
    {
        return $this->hasMany(Resource::class);
    }

    // ─── Help Requests ────────────────────────────────

    /** Help requests submitted by this user (as patient) */
    public function helpRequests(): HasMany
    {
        return $this->hasMany(HelpRequest::class, 'user_id');
    }

    /** Help requests assigned to this user (as doctor) */
    public function assignedHelpRequests(): HasMany
    {
        return $this->hasMany(HelpRequest::class, 'doctor_id');
    }

    // ─── Crisis Reports ───────────────────────────────

    public function crisisReports(): HasMany
    {
        return $this->hasMany(CrisisReport::class, 'user_id');
    }

    // ─── Appointments ─────────────────────────────────

    /** Appointments booked by this user (as patient) */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'patient_id');
    }

    /** Appointments handled by this user (as doctor) */
    public function doctorAppointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'doctor_id');
    }

    /** Schedule slots defined by this user (as doctor) */
    public function doctorSchedules(): HasMany
    {
        return $this->hasMany(DoctorSchedule::class, 'doctor_id');
    }

    // ─── Reviews ──────────────────────────────────────

    public function doctorReviews(): HasMany
    {
        return $this->hasMany(DoctorReview::class, 'doctor_id');
    }
}
