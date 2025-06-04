<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

/**
 * @method static search(string $search)
 *
 * @property mixed $is_blocked
 */
class User extends Authenticatable implements MustVerifyEmail, ShouldQueue
{
    /** @use HasFactory<UserFactory> */
    use CausesActivity, HasFactory, HasRoles, LogsActivity, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'is_blocked',
        'name',
        'email',
        'email_verified_at',
        'password',
        'community_id',
        'membership_id',
        'affiliation',
        'is_subscribed',
        'is_active',
        'unsubscribe_token',
        'remember_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'unsubscribe_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function allergies(): BelongsToMany
    {
        return $this->belongsToMany(Allergy::class, 'allergy_user')
            ->withTimestamps();
    }

    // relationships
    public function community(): BelongsTo
    {
        return $this->belongsTo(Community::class);
    }

    public function membership(): BelongsTo
    {
        return $this->belongsTo(Membership::class);
    }

    public function entitlements(): BelongsToMany
    {
        return $this->belongsToMany(Entitlement::class, 'entitlement_user')
            ->withTimestamps();
    }

    public function comments(): BelongsToMany
    {
        return $this->belongsToMany(Comment::class, 'comment_user')
            ->withTimestamps();
    }

    public function replies(): BelongsToMany
    {
        return $this->belongsToMany(CommentReply::class, 'reply_user')
            ->withTimestamps();
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'event_user')
            ->withTimestamps();
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_user')
            ->withTimestamps();
    }

    public function event(): HasMany
    {
        return $this->hasMany(Event::class, 'user_id')
            ->withTimestamps();
    }

    // Users that this user is following
    public function following(): BelongsToMany
    {
        return $this->belongsToMany(__CLASS__, 'follows', 'follower_id', 'following_id')
            ->withTimestamps();
    }

// Users that are following this user
    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(__CLASS__, 'follows', 'following_id', 'follower_id')
            ->withTimestamps();
    }

    // functions
    public function getFirstNameAttribute(): string
    {
        return explode(' ', $this->name)[0];
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
