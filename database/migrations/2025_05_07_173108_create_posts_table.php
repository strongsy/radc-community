<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->string('post_title');
            $table->mediumText('post_content');
            $table->integer('post_status');
            $table->timestamps();
        });
    }
};
