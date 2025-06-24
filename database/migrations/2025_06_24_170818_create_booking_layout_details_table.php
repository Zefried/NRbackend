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
        Schema::create('booking_layout_details', function (Blueprint $table) {
            $table->id();
            $table->string('master_key_id')->index();
            $table->string('seat_type')->index();
            $table->integer('available_seats')->nullable();
            $table->integer('total_seats')->nullable();
            $table->json('double_seats')->nullable();
            $table->json('female_booked')->nullable();
            $table->json('available_for_female')->nullable();
            $table->json('booked')->nullable();
            $table->timestamps();

            $table->foreign('master_key_id')->references('master_key')->on('booking_layout_masters')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_layout_details');
    }
};
