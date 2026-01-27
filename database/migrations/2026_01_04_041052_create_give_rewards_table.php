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
        Schema::create('give_rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reward_id')->constrained('rewards')->onDelete('cascade');
            $table->string('employee_name');
            $table->string('employee_email');
            $table->string('employee_position');
            $table->string('employee_department');
            $table->date('given_date');
            $table->string('given_by'); 
            $table->string('status')->default('pending'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('give_rewards');
    }
};
