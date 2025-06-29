<?php

use App\Models\EventSessionGuest;
use App\Models\FoodAllergy;
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

        Schema::create('food_allergy_guests', static function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(EventSessionGuest::class, 'event_session_guest_id')->constrained();
            $table->foreignIdFor(FoodAllergy::class, 'food_allergy_id')->constrained();
            $table->timestamps();

            $table->index(['event_session_guest_id', 'food_allergy_id']);
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_allergy_guests');
    }
};
