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
        Schema::create('iot_devices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('home_id')->nullable()->constrained('homes');
            $table->string('ip',50)->nullable();
            $table->double('air_val')->nullable();
            $table->double('left_status')->nullable();
            $table->double('right_status')->nullable();
            $table->string('status',100)->nullable();
            $table->timestamps();
            $table->boolean('deleted')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('iot_devices');
    }
};
