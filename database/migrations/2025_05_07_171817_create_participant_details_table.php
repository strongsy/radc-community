<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('participant_details', static function (Blueprint $table) {
            $table->id();
            $table->morphs('detailable');
            $table->text('notes');
            $table->foreignId('allergy_id');
            $table->foreignId('food_id');
            $table->foreignId('drink_id');
            $table->timestamps();
        });
    }
};
