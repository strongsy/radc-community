<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comments', static function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class, 'user_id')->constrained()->cascadeOnDelete();
            $table->morphs('commentable'); // Creates commentable_id and commentable_type columns
            $table->text('content');
            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
