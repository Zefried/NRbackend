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
        Schema::create('pnr_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pnr_master_id')->index();
            $table->string('pnr')->index();
            $table->string('name');
            $table->string('seat_no');
            $table->string('seat_type');
            $table->string('gender');
            $table->timestamps();

            $table->foreign('pnr_master_id')
                  ->references('id')
                  ->on('pnr_masters')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pnr_details');
    }
};
