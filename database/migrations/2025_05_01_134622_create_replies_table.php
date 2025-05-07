<?php

use App\Models\Email;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('replies', static function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Email::class, 'email_id')->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'user_id')->constrained()->cascadeOnDelete();
            $table->string('reply_subject');
            $table->longText('reply_content');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['email_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('replies');
    }
};
