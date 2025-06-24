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
        Schema::create('boardings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('serving_route_id')->nullable()->constrained('serving_routes')->cascadeOnDelete();
            $table->string('boarding_point')->nullable();
            $table->string('dropping_point')->nullable();
            $table->time('boarding_time')->nullable();
            $table->time('dropping_time')->nullable();
            $table->integer('estimated_duration')->nullable(); // now as int in minutes
            $table->time('arrival_at')->nullable(); // newly added
            $table->integer('delayed')->nullable(); // newly added
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boardings');
    }
};
