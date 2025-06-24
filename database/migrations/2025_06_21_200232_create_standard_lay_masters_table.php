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
        Schema::create('standard_lay_masters', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('operator_id');
            $table->boolean('seater')->default(false)->index();
            $table->boolean('sleeper')->default(false)->index();
            $table->boolean('double_sleeper')->default(false)->index();
            $table->timestamps();

            $table->foreign('operator_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('standard_lay_masters');
    }
};
