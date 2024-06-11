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
        Schema::create('reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('type', ['user', 'device']);
            $table->foreignUuid('user_id')  ;
            $table->foreignUuid('device_id')->nullable()->constrained('iot_devices');
            $table->text('description')->nullable();
            $table->integer('vote')->nullable();
            $table->enum('status', ['pending', 'in progress','resolved'])->default('pending');
            $table->foreignUuid('created_by')->constrained('users');
            $table->string('role_of_creator',50);
            $table->timestamps();
            $table->boolean('deleted')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
