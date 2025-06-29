<?php

use App\Models\Event;
use App\Models\Category;
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

        Schema::create('category_events', static function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Event::class,'event_id')->constrained();
            $table->foreignIdFor(Category::class,'category_id')->constrained();
            $table->timestamps();
            $table->index(['event_id', 'category_id']);
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_events');
    }
};
