<?php

use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('category_articles', static function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Article::class, 'article_id')->constrained()->cascadeOnDelete();
            $table->foreignIdFor(ArticleCategory::class, 'article_category_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }
};
