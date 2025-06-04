<?php
use App\Models\EventTitle;
use App\Models\Status;
use App\Models\User;
use App\Models\Venue;
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
            $table->foreignIdFor(EventTitle::class, 'title_id')->constrained()->cascadeOnDelete();
            $table->mediumText('event_content');
            $table->date('event_date');
            $table->time('event_time');
            /*$table->foreignIdFor(EventCategory::class, 'event_category_id')->constrained()->cascadeOnDelete();*/
            $table->foreignIdFor(Venue::class, 'venue_id')->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Status::class, 'status_id')->default(1)->constrained()->cascadeOnDelete();
            $table->boolean('allow_guests');
            $table->integer('max_guests')->nullable();
            $table->integer('max_attendees')->nullable();
            $table->decimal('user_cost')->default(0.00)->nullable();  // 10 digits total, 2 decimal places
            $table->decimal('guest_cost')->default(0.00)->nullable(); // 10 digits total, 2 decimal places
            $table->string('cover_img')->nullable();
            $table->dateTime('closes_at');
            $table->dateTime('expires_at');
            $table->timestamps();
            $table->softDeletes();
        });
    }
};
