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
        Schema::create('vip_configs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bus_id')->nullable();
            $table->integer('seat_row')->nullable();
            $table->integer('layout')->nullable();
            $table->integer('total_seats')->nullable();
            $table->integer('currently_avl')->nullable();
            $table->json('booked')->nullable();
            $table->string('blocked_real_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vip_configs');
    }
};
