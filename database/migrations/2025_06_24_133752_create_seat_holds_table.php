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
        Schema::create('seat_holds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('seat_type')->nullable()->index();
            $table->string('seat_no')->nullable()->index();
            $table->unsignedBigInteger('operator_id')->nullable()->index();
            $table->date('date')->index();
            $table->string('parent_route')->nullable()->index();
            $table->unsignedBigInteger('serving_route_id')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seat_holds');
    }
};
