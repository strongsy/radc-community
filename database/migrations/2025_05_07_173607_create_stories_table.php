<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stories', static function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class, 'user_id')->constrained()->cascadeOnDelete();
            $table->string('story_title');
            $table->mediumText('story_content');
            $table->integer('story_status')->default(1);
            $table->integer('story_cat');
            $table->string('cover_img');
            $table->timestamps();
        });
    }
};
