<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('comments', static function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class, 'user_id')->constrained();
            $table->morphs('commentable'); // Changed from 'commentable_type' to 'commentable'
            $table->text('content');
            $table->unsignedBigInteger('parent_id')->nullable()->after('commentable_type');
            $table->foreign('parent_id')->references('id')->on('comments')->onDelete('cascade');

            $table->timestamps();
            $table->index(['user_id', 'commentable_id']);
        });


        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
