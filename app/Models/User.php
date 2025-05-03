<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
        'name',
        'email',
        'password',
        'community',
        'membership',
        'affiliation',
        'is_subscribed',
        'is_active',
        'is_blocked',
        'unsubscribe_token',
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
            'password' => 'hashed',
        ];
    }

    public function eventComments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Images uploaded by the user to event galleries
     */
    public function galleryUploads(): HasMany
    {
        return $this->hasMany(Gallery::class);
    }

    /**
     * Notification preferences for this user
     */
    public function notificationPreferences(): HasMany
    {
        return $this->hasMany(UserNotification::class);
    }

    /**
     * Notifications directed to this user
     */
    public function eventNotifications(): HasMany
    {
        return $this->hasMany(EventNotification::class);
    }

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

    // relationships
    public function replies(): HasMany
    {
        return $this->hasMany(Reply::class);
    }
}
