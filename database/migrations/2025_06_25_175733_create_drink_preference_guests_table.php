<?php

use App\Models\DrinkPreference;
use App\Models\EventSessionGuest;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('drink_preference_guests', static function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(EventSessionGuest::class, 'event_session_guest_id')->constrained();
            $table->foreignIdFor(DrinkPreference::class, 'drink_preference_id')->constrained();
            $table->timestamps();

            $table->index(['id', 'event_session_guest_id', 'drink_preference_id']);
        });
    }
};
