<?php

use App\Models\EventCategory;
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
            $table->string('title');
            $table->mediumText('description');
            $table->timestamp('start_datetime');
            $table->timestamp('end_datetime');
            $table->text('location');
            $table->foreignIdFor(EventCategory::class, 'event_category_id')->constrained()->cascadeOnDelete();
            $table->decimal('cost_for_members')->default(0);
            $table->decimal('cost_for_guests')->default(0);
            $table->integer('min_participants')->default(0);
            $table->integer('max_participants')->default(0);
            $table->boolean('guests_allowed')->default(false);
            $table->integer('max_guests_per_user')->default(0);
            $table->foreignIdFor(User::class, 'created_by')->constrained()->cascadeOnDelete();
            $table->string('is_active')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
