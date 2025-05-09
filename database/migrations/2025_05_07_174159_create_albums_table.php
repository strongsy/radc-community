<?php

use App\Models\Gallery;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('albums', static function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class, 'user_id')->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Gallery::class, 'gallery_id')->constrained()->cascadeOnDelete();
            $table->string('album_title');
            $table->text('album_desc');
            $table->string('cover_img');
            $table->timestamps();
        });
    }
};
