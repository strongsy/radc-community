<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('statuses', static function (Blueprint $table) {
            $table->id();
            $table->string('status_type');
            $table->string('status_desc');
            $table->timestamps();
        });
    }
};
