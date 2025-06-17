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
        Schema::create('real_time_seat_holdings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bus_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete();
            $table->integer('seat_no')->nullable();
            $table->string('seat_type')->nullable();
            $table->string('origin')->nullable();
            $table->string('destination')->nullable();
            $table->timestamps();

            $table->index(['bus_id', 'seat_no', 'seat_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('real_time_seat_holdings');
    }
};
