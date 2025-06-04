<?php

use App\Models\ArticleCategory;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', static function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class, 'user_id')->constrained()->cascadeOnDelete();
            $table->string('article_title');
            $table->mediumText('article_content');
            $table->foreignIdFor(ArticleCategory::class, 'article_category_id')->constrained()->cascadeOnDelete();
            $table->string('status_id')->default(1);
            $table->string('cover_img');
            $table->timestamps();
        });
    }
};
