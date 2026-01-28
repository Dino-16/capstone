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
        Schema::create('honeypot_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_enabled')->default(true);
            $table->string('field_name')->default('secondary_email'); // Configurable field name
            $table->timestamps();
        });

        // Insert default record
        DB::table('honeypot_settings')->insert([
            'is_enabled' => true,
            'field_name' => 'secondary_email',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Schema::create('honeypot_logs', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('form_name')->nullable(); // e.g., Login, ApplyNow
            $table->text('payload')->nullable(); // Capture what they tried to send
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('honeypot_logs');
        Schema::dropIfExists('honeypot_settings');
    }
};
