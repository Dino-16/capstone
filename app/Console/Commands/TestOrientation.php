<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Onboarding\Orientation;

class TestOrientation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-orientation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test orientation creation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $orientation = Orientation::create([
                'employee_name' => 'Test Employee',
                'orientation_date' => now(),
                'location' => 'Test Location',
                'facilitator' => 'Test Facilitator',
                'status' => 'scheduled'
            ]);
            
            $this->info('Orientation created successfully with ID: ' . $orientation->id);
        } catch (\Exception $e) {
            $this->error('Error creating orientation: ' . $e->getMessage());
        }
    }
}
