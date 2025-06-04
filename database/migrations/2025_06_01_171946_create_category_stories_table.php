<?php

use App\Models\Story;
use App\Models\StoryCategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('category_stories', static function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Story::class, 'story_id')->constrained()->cascadeOnDelete();
            $table->foreignIdFor(StoryCategory::class, 'story_category_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }
};
