<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('follows', static function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class, 'follower_id')->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'followed_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['follower_id', 'followed_id']);
        });
    }
};
