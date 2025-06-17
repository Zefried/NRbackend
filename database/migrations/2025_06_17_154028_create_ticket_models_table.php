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
        Schema::create('ticket_models', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null')->index();
            $table->unsignedBigInteger('bus_id')->nullable();
            $table->unsignedBigInteger('route_info_id')->nullable()->index();
           
            $table->foreign('bus_id', 'ticket_models_bus_id_foreign')->references('id')->on('add_buses')->onDelete('set null');
            $table->foreign('route_info_id', 'ticket_models_route_info_id_foreign')->references('id')->on('bus_route_info_models')->onDelete('set null');
            
            $table->string('operator_name')->nullable();
            $table->string('driver_name')->nullable();
            $table->string('driver_number')->nullable();
            $table->string('driver_two_name')->nullable();
            $table->string('driver_two_number')->nullable();
          
            $table->string('origin')->nullable();
            $table->string('destination')->nullable();
            $table->string('payment_type')->nullable();
            $table->string('reporting_time')->nullable();
            $table->string('departure_time')->nullable();
            $table->decimal('total_fare', 8, 2)->nullable();
            $table->string('bus_type')->nullable();
            $table->string('plate_no')->nullable();

            $table->string('unique_ticket_no')->nullable()->unique()->index();
            $table->unsignedBigInteger('booking_id')->nullable()->index();
            $table->string('pnr_code')->nullable()->index();
            $table->date('date_of_journey')->nullable()->index();
            $table->string('counter_no')->nullable()->index();
            $table->string('payment_status')->nullable()->default('pending')->index();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_models');
    }
};
