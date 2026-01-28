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
            // Applied position and rating
            $table->string('applied_position')->nullable()->after('candidate_house_street');
            $table->string('department')->nullable()->after('applied_position');
            $table->decimal('rating_score', 5, 2)->nullable()->after('department');
            $table->string('rating_description')->nullable()->after('rating_score');
            
            // Self-scheduling
            $table->string('scheduling_token')->nullable()->unique()->after('interview_schedule');
            $table->boolean('self_scheduled')->default(false)->after('scheduling_token');
            
            // Interview results
            $table->json('interview_scores')->nullable()->after('self_scheduled');
            $table->decimal('interview_total_score', 5, 2)->nullable()->after('interview_scores');
            $table->enum('interview_result', ['pending', 'passed', 'failed'])->default('pending')->after('interview_total_score');
            $table->text('interview_notes')->nullable()->after('interview_result');
            
            // Offering stage
            $table->enum('contract_status', ['pending', 'sent', 'signed', 'declined'])->default('pending')->after('interview_notes');
            $table->datetime('contract_sent_at')->nullable()->after('contract_status');
            $table->datetime('contract_signed_at')->nullable()->after('contract_sent_at');
            $table->boolean('documents_email_sent')->default(false)->after('contract_signed_at');
            $table->datetime('documents_email_sent_at')->nullable()->after('documents_email_sent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->dropColumn([
                'applied_position',
                'department',
                'rating_score',
                'rating_description',
                'scheduling_token',
                'self_scheduled',
                'interview_scores',
                'interview_total_score',
                'interview_result',
                'interview_notes',
                'contract_status',
                'contract_sent_at',
                'contract_signed_at',
                'documents_email_sent',
                'documents_email_sent_at',
            ]);
        });
    }
};
