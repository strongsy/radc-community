<?php

use App\Models\Event;
use App\Models\EventCategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('category_event', static function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(EventCategory::class, 'event_category_id')->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Event::class, 'event_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['event_id', 'event_category_id']);
        });
    }
};
