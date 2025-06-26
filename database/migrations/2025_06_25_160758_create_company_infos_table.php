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
        Schema::create('company_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operator_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('company_name')->nullable()->index();
            $table->boolean('ac_status')->nullable()->index();
            $table->text('office_address')->nullable();
            $table->integer('no_of_buses')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_infos');
    }
};
