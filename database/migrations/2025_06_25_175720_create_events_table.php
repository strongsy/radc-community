<?php

use App\Models\Title;
use App\Models\User;
use App\Models\Venue;
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

        Schema::create('events', static function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class, 'user_id')->constrained();
            $table->foreignIdFor(Title::class, 'title_id')->constrained();
            $table->foreignIdFor(Venue::class, 'venue_id')->constrained();
            $table->text('description');
            $table->integer('max_serials')->default(1)->comment('The maximum number of serials allowed for this event. min:1 max:40');
            $table->date('start_date');
            $table->date('end_date');
            $table->date('rsvp_closes_at');
            $table->timestamps();
            $table->index(['id', 'user_id']);
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
