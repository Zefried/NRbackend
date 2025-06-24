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
        Schema::create('pnr_masters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('operator_id')->index();
            $table->string('parent_route')->index();
            $table->date('date')->index();
            $table->string('pnr')->unique()->index();
            $table->enum('payment_status', ['paid', 'unpaid'])->default('unpaid');
            $table->enum('pnr_status', ['confirmed', 'cancelled', 'pending'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pnr_masters');
    }
};
