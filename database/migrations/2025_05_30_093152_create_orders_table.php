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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('bus_id')->nullable();
            $table->string('bus_name_plate')->nullable();
            $table->string('user_id')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('user_phone')->nullable();
            $table->string('gender')->nullable();
            $table->string('payment_status')->nullable()->default('pending');
            $table->integer('age')->nullable();
            $table->string('boarding')->nullable();
            $table->string('dropping')->nullable();
            $table->double('amount')->nullable();
            $table->string('order_status')->nullable()->default('pending');
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
