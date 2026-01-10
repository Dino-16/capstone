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
        Schema::create('job_lists', function (Blueprint $table) {
            $table->id();
            $table->string('position');
            $table->string('department');
            $table->text('description');
            $table->text('qualifications');
            $table->enum('type', ['On-Site', 'Remote', 'Hybrid'])->default('On-Site');
            $table->enum('arrangement', ['Full-Time', 'Part-Time'])->default('Full-Time');
            $table->enum('status', ['Active', 'Inactive'])->default('Inactive');
            $table->date('expiration_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_lists');
    }
};
