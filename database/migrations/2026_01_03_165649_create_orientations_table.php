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
        Schema::create('orientations', function (Blueprint $table) {
            $table->id();
            $table->string('employee_name')->unique();
            $table->string('email')->nullable();
            $table->string('position')->nullable();
            $table->datetime('orientation_date');
            $table->string('location');
            $table->string('facilitator');
            $table->text('notes')->nullable();
            $table->enum('status', ['scheduled', 'completed', 'cancelled'])->default('scheduled');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orientations');
    }
};
