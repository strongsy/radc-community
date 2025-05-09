<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('food_preferences', static function (Blueprint $table) {
            $table->id();
            $table->string('food_type');
            $table->timestamps();
        });
    }
};
