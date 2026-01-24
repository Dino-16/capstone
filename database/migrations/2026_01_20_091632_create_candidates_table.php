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
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->string('candidate_name');
            $table->string('candidate_email');
            $table->string('candidate_phone');
            $table->string('candidate_sex');
            $table->date('candidate_birth_date');
            $table->string('candidate_civil_status');
            $table->integer('candidate_age');
            $table->string('candidate_region');
            $table->string('candidate_province');
            $table->string('candidate_city');
            $table->string('candidate_barangay');
            $table->string('candidate_house_street');
            $table->json('skills')->nullable();
            $table->json('experience')->nullable();
            $table->json('education')->nullable();
            $table->string('resume_url')->nullable();
            $table->string('status')->default('scheduled');
            $table->dateTime('interview_schedule')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};
