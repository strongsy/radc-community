<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Event extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'title_id',
        'venue_id',
        'description',
        'max_serials',
        'start_date',
        'end_date',
        'rsvp_closes_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'rsvp_closes_at' => 'date',
    ];

    /**
     * Define an inverse one-to-one or many relationship with the User model.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Define an inverse one-to-one or many relationship with the Title model.
     *
     * @return BelongsTo
     */
    public function title(): BelongsTo
    {
        return $this->belongsTo(Title::class);
    }

    /**
     * Define an inverse one-to-one or many relationship with the Venue model.
     *
     * @return BelongsTo
     */
    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    /**
     * Defines inverse many relationships with the Category model.
     * @return BelongsToMany
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_events');
    }

    /**
     * Defines a one-to-many relationship with the EventSession model.
     */
    public function eventSessions(): HasMany
    {
        return $this->hasMany(EventSession::class);
    }

    /**
     * Defines a polymorphic one-to-many relationship with the Gallery model.
     */
    public function galleries(): MorphMany
    {
        return $this->morphMany(Gallery::class, 'galleryable');
    }

    /**
     * Establishes a polymorphic one-to-many relationship with the Comment model.
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * Define a polymorphic one-to-many relationship.
     *
     * @return MorphMany
     */
    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    /**
     * Define a polymorphic one-to-many relationship.
     *
     * @return MorphMany
     */
    public function dislikes(): MorphMany
    {
        return $this->morphMany(Dislike::class, 'dislikeable');
    }

    /**
     * Register media conversions for the model.
     *
     * This method defines various media transformations, such as resizing and sharpening,
     * ensuring they are queued for processing.
     *
     * @param Media|null $media An optional Media instance to apply conversions to.
     * @return void
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('preview')
            ->fit(Fit::Contain, 300, 300)
            ->queued();

        $this
            ->addMediaConversion('event')
            /*->crop(800, 600, CropPosition::Top)*/
            ->fit(Fit::Contain, 800, 600)
            ->sharpen(10)
            ->queued();
    }

    /**
     * Scope a query to clean expired entries by setting the 'deleted_at' timestamp.
     */
    public function scopeCleanExpired($query): void
    {
        $query->where('expires_at', '<=', now())
            ->whereNull('deleted_at')
            ->update(['deleted_at' => now()]);

    }

    /**
     * Retrieve the last record from a collection of all records.
     *
     * @return mixed
     */
    public static function last(): mixed
    {
        return static::all()->last();
    }
}
