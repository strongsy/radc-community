<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Venue extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'venue',
        'address',
        'city',
        'county',
        'post_code',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['venue', 'address', 'city', 'county', 'post_code'])
            ->setDescriptionForEvent(fn (string $eventName) => "This venue has been $eventName")
            ->useLogName('user')
            ->logOnlyDirty();
        // Chain fluent methods for configuration options
    }
}
