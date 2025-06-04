<?php

use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('category_news', static function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(News::class, 'news_id')->constrained()->cascadeOnDelete();
            $table->foreignIdFor(NewsCategory::class, 'news_category_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }
};
