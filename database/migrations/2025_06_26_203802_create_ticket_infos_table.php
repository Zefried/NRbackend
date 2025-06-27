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
        Schema::create('ticket_infos', function (Blueprint $table) {
            $table->id();
            $table->string('pnr')->nullable()->index();
            $table->string('booking_id')->nullable()->index();
            $table->decimal('total_fare', 8, 2)->nullable();
            $table->decimal('base_fare', 8, 2)->nullable();
            $table->decimal('taxes', 8, 2)->nullable();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->string('name')->nullable();
            $table->string('gender')->nullable();
            $table->string('parent_route')->nullable()->index();
            $table->unsignedBigInteger('operator_id')->nullable()->index();
            $table->date('date_of_journey')->nullable()->index();
            $table->string('source')->nullable()->index();
            $table->string('destination')->nullable()->index();
            $table->string('boarding_point')->nullable();
            $table->string('dropping_point')->nullable();
            $table->string('duration')->nullable();
            $table->json('passengers')->nullable();
            $table->string('payment_status')->default('pending')->nullable()->index();
            $table->timestamps();



        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_infos');
    }
};
