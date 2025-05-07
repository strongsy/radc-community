<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->string('ratable');
            $table->integer('rating');
            $table->text('rating_review')->nullable();
            $table->timestamps();
        });
    }
};
