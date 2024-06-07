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
        Schema::create('addresses', function (Blueprint $table) {
            $table->uuid('id')->primary()->foreign('id')->references('id')->on('homes')->onDelete('cascade');
            $table->string('unit_number')->nullable();
            $table->string('street_number')->nullable();
            $table->string('address_line');
            $table->string('ward');
            $table->string('district');
            $table->string('city');
            $table->string('province')->nullable();
            $table->string('country_name');
            $table->timestamps();
            $table->boolean('deleted')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
