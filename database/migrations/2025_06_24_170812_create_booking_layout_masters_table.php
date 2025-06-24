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
        Schema::create('booking_layout_masters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('operator_id')->index();
            $table->date('date')->index();
            $table->string('parent_route')->index();
            $table->string('master_key')->unique()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_layout_masters');
    }
};
