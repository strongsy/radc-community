<?php

use App\Models\Category;
use App\Models\NewsCategory;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news', static function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class, 'user_id')->constrained()->cascadeOnDelete();
            $table->string('news_title');
            $table->mediumText('news_content');
            $table->foreignIdFor(NewsCategory::class, 'news_category_id')->constrained()->cascadeOnDelete();;
            $table->integer('news_status')->default(1);
            $table->dateTime('release_at');
            $table->dateTime('expires_at');
            $table->string('cover_img');
            $table->timestamps();
        });
    }
};
