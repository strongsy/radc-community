<?php

use App\Models\User;
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

        Schema::create('dislikes', static function (Blueprint $table) {
            $table->id();
            $table->ForeignIdFor(User::class, 'user_id')->constrained();
            $table->morphs('dislikeable');
            $table->timestamps();
            $table->index(['user_id', 'dislikeable_id']);
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dislikes');
    }
};
