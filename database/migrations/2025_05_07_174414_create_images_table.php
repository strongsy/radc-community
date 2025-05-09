<?php

use App\Models\Album;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('images', static function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class, 'user_id')->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Album::class, 'album_id')->constrained()->cascadeOnDelete();
            $table->string('img_path');
            $table->string('img_name');
            $table->string('img_caption');
            $table->timestamps();
        });
    }
};
