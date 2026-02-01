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
        Schema::table('job_lists', function (Blueprint $table) {
            $table->string('location')->nullable()->default('Ever Gotesco Commonwealth')->after('arrangement');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_lists', function (Blueprint $table) {
            $table->dropColumn('location');
        });
    }
};
