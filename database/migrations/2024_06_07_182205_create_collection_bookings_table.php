<?php

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
        Schema::create('collection_bookings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('home_id')->references('id')->on('homes');
            $table->foreignUuid('schedule_id')->references('id')->on('schedules');
            $table->string('status');
            $table->timestamps();
            $table->boolean('deleted')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collection_bookings');
    }
};
