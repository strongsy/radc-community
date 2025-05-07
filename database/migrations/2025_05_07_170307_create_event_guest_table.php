<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('event_guest', static function (Blueprint $table) {
            $table->id();
            $table->bigInteger('guest_id');
            $table->bigInteger('event_id');
            $table->timestamps();
        });
    }
};
