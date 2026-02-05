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
        Schema::table('candidates', function (Blueprint $table) {
            $table->string('candidate_sex')->nullable()->change();
            $table->date('candidate_birth_date')->nullable()->change();
            $table->string('candidate_civil_status')->nullable()->change();
            $table->integer('candidate_age')->nullable()->change();
            $table->string('candidate_region')->nullable()->change();
            $table->string('candidate_province')->nullable()->change();
            $table->string('candidate_city')->nullable()->change();
            $table->string('candidate_barangay')->nullable()->change();
            $table->string('candidate_house_street')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->string('candidate_sex')->nullable(false)->change();
            $table->date('candidate_birth_date')->nullable(false)->change();
            $table->string('candidate_civil_status')->nullable(false)->change();
            $table->integer('candidate_age')->nullable(false)->change();
            $table->string('candidate_region')->nullable(false)->change();
            $table->string('candidate_province')->nullable(false)->change();
            $table->string('candidate_city')->nullable(false)->change();
            $table->string('candidate_barangay')->nullable(false)->change();
            $table->string('candidate_house_street')->nullable(false)->change();
        });
    }
};
