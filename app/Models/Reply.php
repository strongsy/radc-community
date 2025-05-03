<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Reply extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'email_id',
        'user_id',
        'subject',
        'message',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['email_id', 'sender_id', 'subject'])
            ->setDescriptionForEvent(fn (string $eventName) => "This reply has been $eventName")
            ->useLogName('reply')
            ->logOnlyDirty();
        // Chain fluent methods for configuration options
    }
}
