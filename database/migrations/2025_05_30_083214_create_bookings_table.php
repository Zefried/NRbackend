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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->unsignedBigInteger('order_id')->nullable()->index();
            $table->unsignedBigInteger('bus_id')->nullable();
            $table->string('gender')->nullable();
            $table->string('transaction_id')->nullable()->index();
            $table->string('seat_type')->nullable()->index();
            $table->string('user_phone')->nullable()->index();
            $table->string('seat_no')->nullable();
            $table->string('boarding')->nullable();
            $table->string('dropping')->nullable();
            $table->double('amount')->nullable();
            $table->string('payment_status')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
            $table->foreign('bus_id')->references('id')->on('add_buses')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
