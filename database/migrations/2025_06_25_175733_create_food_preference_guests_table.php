<?php

use App\Models\EventSessionGuest;
use App\Models\FoodPreference;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('food_preference_guests', static function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(EventSessionGuest::class, 'event_session_guest_id')->constrained();
            $table->foreignIdFor(FoodPreference::class, 'food_preference_id')->constrained();
            $table->timestamps();
            $table->index(['event_session_guest_id', 'food_preference_id']);
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_preference_guests');
    }
};
