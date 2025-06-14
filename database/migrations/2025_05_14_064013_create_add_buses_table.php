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
        Schema::create('add_buses', function (Blueprint $table) {
            $table->id();
            $table->string('unique_bus_id')->nullable();
            $table->string('operator_name')->nullable();
            $table->string('bus_name')->nullable();
            $table->boolean('sleeper')->default(false);
            $table->boolean('seater')->default(false);
            $table->boolean('vip')->default(false);
            $table->boolean('Ac_type')->default(true);
            $table->string('bus_plate_number')->nullable()->index();
            $table->string('driver_name')->nullable();
            $table->string('driver_phone')->nullable();
            $table->string('driverTwo_name')->nullable();
            $table->string('driverTwo_phone')->nullable();
            $table->string('handyman_name')->nullable();
            $table->string('handyman_phone')->nullable();
            $table->string('bus_config')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('add_buses');
    }
};
