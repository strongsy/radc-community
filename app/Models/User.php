<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, LogsActivity, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'community_id',
        'membership_id',
        'affiliation',
        'is_approved',
        'approved_at',
        'approved_by',
        'is_subscribed',
        'is_blocked',
        'unsubscribe_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'approved_at' => 'datetime',
        'is_approved' => 'boolean',
        'is_subscribed' => 'boolean',
        'is_blocked' => 'boolean',
        'password' => 'hashed',
    ];

    public function community(): BelongsTo
    {
        return $this->belongsTo(Community::class);
    }

    public function membership(): BelongsTo
    {
        return $this->belongsTo(Membership::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(__CLASS__, 'approved_by');
    }

    public function entitlements(): BelongsToMany
    {
        return $this->belongsToMany(Entitlement::class, 'entitlement_users');
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function eventSessions(): BelongsToMany
    {
        return $this->belongsToMany(EventSession::class, 'event_session_users');
    }

    public function eventSessionGuests(): HasMany
    {
        return $this->hasMany(EventSessionGuest::class);
    }

    public function stories(): HasMany
    {
        return $this->hasMany(Story::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function news(): HasMany
    {
        return $this->hasMany(News::class);
    }

    public function gallery(): HasMany
    {
        return $this->hasMany(Gallery::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function dislikes(): HasMany
    {
        return $this->hasMany(Dislike::class);
    }

    public function contactEmailReplies(): HasMany
    {
        return $this->hasMany(ContactEmailReply::class);
    }

    // functions
    public function getFirstNameAttribute(): string
    {
        if (empty($this->name)) {
            return 'Friend';
        }

        $nameParts = explode(' ', trim($this->name));
        return $nameParts[0] ?? 'Friend';
    }

    protected static function booted(): void
    {
        static::creating(static function ($user) {
            $user->unsubscribe_token = $user->unsubscribe_token ?? Str::random(32);
        });

        static::updating(static function ($user) {
            if (! $user->unsubscribe_token) {
                $user->unsubscribe_token = Str::random(32);
            }
        });
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn (string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'is_blocked', 'is_active', 'is_subscribed', 'email', 'community', 'membership'])
            ->setDescriptionForEvent(fn (string $eventName) => "This user has been $eventName")
            ->useLogName('user')
            ->logOnlyDirty();
        // Chain fluent methods for configuration options
    }
}
