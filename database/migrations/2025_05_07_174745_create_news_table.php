<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->string('news_title');
            $table->mediumText('news_content');
            $table->integer('news_cat');
            $table->integer('news_status');
            $table->dateTime('release_at');
            $table->dateTime('expires_at');
            $table->string('cover_img');
            $table->timestamps();
        });
    }
};
