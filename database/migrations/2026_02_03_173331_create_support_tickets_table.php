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
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('requester_name');
            $table->string('requester_email');
            $table->string('requester_position');
            $table->string('subject');
            $table->text('description');
            $table->string('priority')->default('Low'); // Low, Medium, High
            $table->string('status')->default('Pending'); // Pending, Approved, Rejected, Resolved
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};
