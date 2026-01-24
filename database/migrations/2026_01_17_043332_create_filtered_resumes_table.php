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
        Schema::create('filtered_resumes', function (Blueprint $table) {
            $table->id();
            // Relationship to the applications table
            $table->foreignId('application_id')->constrained()->onDelete('cascade');
            
            // AI Extracted Data
            $table->json('skills')->nullable();
            $table->json('experience')->nullable();
            $table->json('education')->nullable();
            
            // Scoring
            $table->integer('rating_score')->default(0);
            $table->string('qualification_status')->nullable(); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('filtered_resumes');
    }
};
