<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            // Modify the status column to include 'shortlisted', 'rejected', 'for_interview', etc.
            // Using raw SQL to avoid doctrine/dbal dependency for enum modification
            DB::statement("ALTER TABLE applications MODIFY COLUMN status ENUM('active', 'drafted', 'shortlisted', 'rejected', 'for_interview', 'hired') DEFAULT 'active'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            // Revert back to original enum values
            DB::statement("ALTER TABLE applications MODIFY COLUMN status ENUM('active', 'drafted') DEFAULT 'active'");
        });
    }
};
