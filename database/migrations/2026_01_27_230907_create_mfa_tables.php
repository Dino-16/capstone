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
        Schema::create('mfa_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_global_enabled')->default(true);
            $table->boolean('hr_staff_enabled')->default(true);
            $table->boolean('hr_manager_enabled')->default(true);
            $table->timestamps();
        });

        // Insert default record
        DB::table('mfa_settings')->insert([
            'is_global_enabled' => true,
            'hr_staff_enabled' => true,
            'hr_manager_enabled' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Schema::create('mfa_logs', function (Blueprint $table) {
            $table->id();
            $table->string('email')->nullable();
            $table->string('role')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('action'); // login_attempt, mfa_sent, mfa_verified, mfa_failed, login_blocked (role)
            $table->string('status'); // success, failed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mfa_logs');
        Schema::dropIfExists('mfa_settings');
    }
};
