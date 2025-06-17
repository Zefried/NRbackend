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
            $table->unsignedBigInteger('bus_id')->nullable()->index();
            $table->unsignedBigInteger('route_info_id')->nullable()->index();
            $table->string('pnr_code')->nullable()->index();
            $table->string('booking_status')->nullable();
            $table->string('payment_status')->nullable()->index();
            $table->string('counter_no')->nullable();
            $table->double('total_fare')->nullable();
            $table->integer('total_seats')->nullable();
            $table->string('chalan_status')->nullable();
            $table->string('chalan_no')->nullable()->index();
            $table->string('transaction_id')->nullable()->index();
            $table->string('origin')->nullable();
            $table->string('destination')->nullable();
            $table->date('date_of_journey')->nullable();
            $table->timestamps();
            $table->index('created_at');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('bus_id')->references('id')->on('add_buses')->onDelete('set null');
            $table->foreign('route_info_id')->references('id')->on('bus_route_infos')->onDelete('set null');
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
