<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('venues', static function (Blueprint $table) {
            $table->id();
            $table->string('venue');
            $table->string('address');
            $table->string('city');
            $table->string('county');
            $table->string('post_code');
            $table->timestamps();
            $table->unique(['venue', 'address'], 'venue_address_unique');

        });
    }

    public function down(): void
    {
        Schema::table('venues', static function (Blueprint $table) {
            $table->dropUnique('venue_address_unique');
        });
    }
};
