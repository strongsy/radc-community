<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->string('article_title');
            $table->mediumText('article_content');
            $table->string('article_cat');
            $table->string('article_status');
            $table->string('cover_img');
            $table->timestamps();
        });
    }
};
