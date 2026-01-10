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
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->string('employee_name');
            $table->string('email');
            $table->date('evaluation_date');
            $table->string('evaluator_name');
            $table->string('position')->nullable();
            $table->string('department')->nullable();
            $table->date('employment_date')->nullable();
            $table->string('evaluation_type')->default('Regular');
            $table->text('performance_areas')->nullable();
            $table->integer('overall_score')->default(0);
            $table->integer('job_knowledge')->default(0);
            $table->integer('work_quality')->default(0);
            $table->integer('initiative')->default(0);
            $table->integer('communication')->default(0);
            $table->integer('dependability')->default(0);
            $table->integer('attendance')->default(0);
            $table->text('strengths')->nullable();
            $table->text('areas_for_improvement')->nullable();
            $table->text('comments')->nullable();
            $table->text('employee_comments')->nullable();
            $table->string('status')->default('Pending');
            $table->string('evaluator_signature')->nullable();
            $table->string('employee_signature')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};
