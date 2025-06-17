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
        Schema::create('p_n_r_models', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id')->nullable()->index();
            $table->string('pnr_code')->nullable()->index();
            $table->string('seat_type')->nullable();
            $table->string('seat_no')->nullable();
            $table->string('name')->nullable();
            $table->string('gender')->nullable();
            $table->integer('age')->nullable();
            $table->index('created_at');
            $table->timestamps();

            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('set null');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('p_n_r_models');
    }
};
