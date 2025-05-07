<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stories', function (Blueprint $table) {
            $table->id();
            $table->string('story_title');
            $table->mediumText('story_content');
            $table->integer('story_status');
            $table->integer('story_cat');
            $table->string('cover_img');
            $table->timestamps();
        });
    }
};
