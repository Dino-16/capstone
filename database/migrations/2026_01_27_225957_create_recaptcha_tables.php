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
        Schema::create('recaptcha_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
        });

        // Insert default record
        DB::table('recaptcha_settings')->insert([
            'is_enabled' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Schema::create('recaptcha_logs', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('status'); // success, failed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recaptcha_logs');
        Schema::dropIfExists('recaptcha_settings');
    }
};
