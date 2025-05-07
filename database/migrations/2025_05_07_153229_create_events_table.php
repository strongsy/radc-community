<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class, 'user_id')->constrained()->cascadeOnDelete();;
            $table->string('event_title');
            $table->mediumText('event_content');
            $table->date('event_date');
            $table->time('event_time');
            $table->string('event_loc');
            $table->bigInteger('event_cat');
            $table->bigInteger('event_status');
            $table->boolean('allow_guests');
            $table->integer('max_guests');
            $table->integer('max_attendees');
            $table->decimal('user_cost');
            $table->decimal('guest_cost');
            $table->string('cover_img');
            $table->dateTime('closes_at');
            $table->dateTime('expires_at');
            $table->timestamps();
            $table->softDeletes();
        });
    }
};
