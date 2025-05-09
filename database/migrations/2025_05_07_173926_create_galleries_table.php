<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('galleries', static function (Blueprint $table) {
            $table->id();
            $table->string('gallery_title');
            $table->text('gallery_desc');
            $table->timestamps();
        });
    }
};
