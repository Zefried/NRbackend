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
        Schema::create('seater_fares', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('serving_route_id')->nullable()->index();
            $table->foreign('serving_route_id')->references('id')->on('serving_routes')->onDelete('cascade');

            $table->decimal('fare', 8, 2);
            $table->decimal('discount_flat', 8, 2)->nullable();
            $table->decimal('discount_percent', 5, 2)->nullable();
            $table->decimal('final_fare', 8, 2)->nullable();
            $table->string('type')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seater_fares');
    }
};
