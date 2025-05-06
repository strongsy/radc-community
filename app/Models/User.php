<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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

   //relationships
    /**
     * Get the events organized by the user.
     */
    public function organizedEvents()
    {
        return $this->hasMany(Event::class);
    }

    /**
     * Get the events the user is attending.
     */
    public function attendingEvents(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_attendees')
            ->withPivot('is_attending')
            ->withTimestamps();
    }

    //Relationships

    /**
     * Get the guests the user has invited to events.
     */
    public function eventGuests(): HasMany
    {
        return $this->hasMany(EventGuest::class);
    }

    /**
     * Get the posts created by the user.
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Get the articles created by the user.
     */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    /**
     * Get the stories created by the user.
     */
    public function stories(): HasMany
    {
        return $this->hasMany(Story::class);
    }

    /**
     * Get the galleries created by the user.
     */
    public function galleries(): HasMany
    {
        return $this->hasMany(Gallery::class);
    }

    /**
     * Get the comments created by the user.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get the images uploaded by the user.
     */
    public function images(): HasMany
    {
        return $this->hasMany(Image::class);
    }

    /**
     * Get the likes given by the user.
     */
    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function commentReplies(): HasMany
    {
        return $this->hasMany(CommentReply::class);
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Reply::class);
    }


    //functions

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
