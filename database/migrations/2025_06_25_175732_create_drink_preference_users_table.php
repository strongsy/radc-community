<?php

use App\Models\DrinkPreference;
use App\Models\EventSessionUser;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('drink_preference_users', static function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(EventSessionUser::class, 'event_session_user_id')->constrained();
            $table->foreignIdFor(DrinkPreference::class, 'drink_preference_id')->constrained();
            $table->timestamps();
        });
    }
};
