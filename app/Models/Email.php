<?php

namespace App\Models;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Activitylog\Traits\LogsActivity;

class Email extends Model implements shouldQueue
{
    use CausesActivity, HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'sender_name',
        'sender_email',
        'subject',
        'message',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['sender_name', 'sender_email', 'subject'])
            ->setDescriptionForEvent(fn (string $eventName) => "This email has been {$eventName}");
        // Chain fluent methods for configuration options
    }

    public function reply(): HasOne
    {
        return $this->hasOne(Reply::class);
    }
}
