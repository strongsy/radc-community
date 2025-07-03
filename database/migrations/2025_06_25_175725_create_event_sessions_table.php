<?php

use App\Models\Event;
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

        Schema::create('event_sessions', static function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Event::class, 'event_id')->constrained();
            $table->string('name', 255);
            $table->string('location', 255);
            $table->string('description', 1000);
            $table->date('start_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('capacity')->nullable();
            $table->float('cost', 4)->nullable();
            $table->float('grant', 4)->nullable();
            $table->boolean('allow_guests')->default(false);
            $table->timestamps();
            $table->index(['id', 'event_id']);
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_sessions');
    }
};
