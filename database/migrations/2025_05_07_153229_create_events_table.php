<?php

use App\Models\Category;
use App\Models\Status;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', static function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class, 'user_id')->constrained()->cascadeOnDelete();
            $table->string('event_title');
            $table->mediumText('event_content');
            $table->date('event_date');
            $table->time('event_time');
            $table->string('event_loc');
            $table->foreignIdFor(Category::class, 'category_id')->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Status::class, 'status_id')->constrained()->cascadeOnDelete();
            $table->boolean('allow_guests');
            $table->integer('max_guests');
            $table->integer('max_attendees');
            $table->integer('user_cost')->default(0);
            $table->integer('guest_cost')->default(0);
            $table->string('cover_img');
            $table->dateTime('closes_at');
            $table->dateTime('expires_at');
            $table->timestamps();
            $table->softDeletes();
        });
    }
};
