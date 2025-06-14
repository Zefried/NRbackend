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
        Schema::create('bus_route_info_models', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bus_id')->nullable(); // this foreight 
            $table->string('origin')->nullable()->index();
            $table->string('destination')->nullable()->index();
            $table->string('rest_point')->nullable();
            $table->string('rest_duration')->nullable();
            $table->json('routes')->nullable();
            $table->json('boarding_points')->nullable();
            $table->json('dropping_points')->nullable();
            $table->json('start_point')->nullable();
            $table->json('final_drop_point')->nullable();
            $table->string('estimated_duration')->nullable();
            $table->float('distance_km')->nullable();
            $table->string('route_code')->nullable();
            $table->float('seater_base_price')->nullable();
            $table->float('sleeper_base_price')->nullable();
            $table->float('seater_discount')->nullable();
            $table->float('sleeper_discount')->nullable();
            $table->float('seater_offer_price')->nullable();
            $table->float('sleeper_offer_price')->nullable();
            $table->json('offline_dates')->nullable();
            $table->timestamps();

            $table->foreign('bus_id')->references('id')->on('buses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bus_route_info_models');
    }
};
