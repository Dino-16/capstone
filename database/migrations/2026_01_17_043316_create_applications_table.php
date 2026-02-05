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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            
            $table->string('applied_position');
            $table->string('department');
            $table->enum('status', ['active', 'drafted'])->default('active');

            // Personal Info
            $table->string('first_name');
            $table->string('middle_name');
            $table->string('last_name');
            $table->string('suffix_name')->nullable();
            $table->integer('age');
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female']);
            $table->string('civil_status')->nullable();
            // Contact & Address
            $table->string('email');
            $table->string('phone');
            $table->string('region');
            $table->string('province');
            $table->string('city');
            $table->string('barangay');
            $table->text('house_street');
            
            // File path
            $table->string('resume_path');

            $table->boolean('agreed_to_terms')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
