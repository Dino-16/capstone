<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Applicants\FilteredResume;

class FixQualificationStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:qualification-status {--dry-run : Show what would be changed without actually changing it}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix misaligned qualification status based on rating scores';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->info('DRY RUN MODE - No changes will be made');
            $this->line('');
        }

        $this->info('Fixing qualification status alignment...');
        $this->line('');

        $resumes = FilteredResume::whereNotNull('rating_score')->get();
        
        if ($resumes->isEmpty()) {
            $this->warn('No filtered resumes found with rating scores.');
            return 0;
        }

        $this->info("Found {$resumes->count()} filtered resumes to process");
        $this->line('');

        $updated = 0;
        $unchanged = 0;

        $progressBar = $this->output->createProgressBar($resumes->count());
        $progressBar->start();

        foreach ($resumes as $resume) {
            $oldStatus = $resume->qualification_status;
            $newStatus = $this->calculateQualificationStatus($resume->rating_score);

            if ($oldStatus !== $newStatus) {
                if (!$isDryRun) {
                    $resume->qualification_status = $newStatus;
                    $resume->save();
                }
                $updated++;
                
                if ($isDryRun) {
                    $this->line("\nWould update Application #{$resume->application_id}:");
                    $this->line("  Score: {$resume->rating_score}");
                    $this->line("  Old: {$oldStatus}");
                    $this->line("  New: {$newStatus}");
                }
            } else {
                $unchanged++;
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->line('');
        $this->line('');

        if ($isDryRun) {
            $this->info("Results (Dry Run):");
            $this->line("  Would update: {$updated} records");
            $this->line("  Already correct: {$unchanged} records");
            $this->line('');
            $this->info("Run without --dry-run to apply changes:");
            $this->line("  php artisan fix:qualification-status");
        } else {
            $this->info('Results:');
            $this->line("  ✓ Updated: {$updated} records");
            $this->line("  ✓ Already correct: {$unchanged} records");
            $this->line('');
            $this->info('All qualification statuses are now aligned with rating scores!');
        }

        return 0;
    }

    /**
     * Calculate qualification status from rating score
     */
    private function calculateQualificationStatus($score)
    {
        if ($score >= 90) {
            return 'Exceptional';
        } elseif ($score >= 80) {
            return 'Highly Qualified';
        } elseif ($score >= 70) {
            return 'Qualified';
        } elseif ($score >= 60) {
            return 'Moderately Qualified';
        } elseif ($score >= 50) {
            return 'Marginally Qualified';
        } else {
            return 'Not Qualified';
        }
    }
}
