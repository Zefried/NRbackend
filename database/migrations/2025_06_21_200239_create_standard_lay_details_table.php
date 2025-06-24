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
        Schema::create('standard_lay_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('layout_id');
            $table->string('type'); // seater, sleeper, upper, lower
            $table->integer('row');
            $table->integer('col');
            $table->timestamps();

            // Connect to standard_lay_masters table
           $table->foreign('layout_id')->references('id')->on('standard_lay_masters')->onDelete('cascade');
          
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('standard_lay_details');
    }
};
