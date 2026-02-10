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
        Schema::table('candidates', function (Blueprint $table) {
            // Modify interview_result to be a string to allow dynamic values like 'passed_initial', etc.
            // Using raw SQL to avoid doctrine/dbal dependency for column modification
            DB::statement("ALTER TABLE candidates MODIFY COLUMN interview_result VARCHAR(255) DEFAULT 'pending'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            // Revert back to original enum values (might truncate data if new values exist, but this is rollback)
            DB::statement("ALTER TABLE candidates MODIFY COLUMN interview_result ENUM('pending', 'passed', 'failed') DEFAULT 'pending'");
        });
    }
};
