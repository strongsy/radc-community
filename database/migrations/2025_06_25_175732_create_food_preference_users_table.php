<?php

use App\Models\EventSessionUser;
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

        Schema::create('food_preference_users', static function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(EventSessionUser::class, 'event_session_user_id')->constrained();
            $table->foreignIdFor(FoodPreference::class, 'food_preference_id')->constrained();
            $table->timestamps();

            $table->index(['event_session_user_id', 'food_preference_id']);
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_preference_users');
    }
};
