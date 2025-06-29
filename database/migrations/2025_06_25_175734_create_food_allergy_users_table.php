<?php

use App\Models\EventSessionUser;
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

        Schema::create('food_allergy_users', static function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(EventSessionUser::class, 'event_session_user_id')->constrained();
            $table->foreignidFor(FoodAllergy::class, 'food_allergy_id')->constrained();
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_allergy_users');
    }
};
