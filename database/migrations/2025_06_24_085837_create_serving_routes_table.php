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
        Schema::create('serving_routes', function (Blueprint $table) {
         	$table->id();
            $table->unsignedBigInteger('operator_id')->nullable()->index();
            $table->string('source')->nullable()->index();
            $table->string('destination')->nullable()->index();
            $table->string('parent_route')->nullable()->index();
            $table->string('direction')->nullable()->index();
            $table->time('from')->nullable()->index();
            $table->time('to')->nullable()->index();
            $table->json('unavailable_dates')->nullable();
            $table->string('state')->nullable()->index();
            $table->string('active_status')->default('online')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('serving_routes');
    }
};
