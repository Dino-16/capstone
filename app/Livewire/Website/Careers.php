<?php

namespace App\Livewire\Website;

use Livewire\Component;
use App\Models\Recruitment\JobListing;

class Careers extends Component
{
    public $showDetails = false;
    public $selectedJob;
    public $jobs;
    public $search = '';

    public function mount()
    {
        $this->jobs = JobListing::where('status', 'Active')->latest()->get();
    }

    public function viewDetails($id)
    {
        $this->selectedJob = JobListing::where('status', 'Active')->find($id);
        $this->showDetails = true;

        // Move selected job to the top
        $this->jobs = $this->jobs->reject(fn($job) => $job->id === $id);
        if ($this->selectedJob) {
            $this->jobs->prepend($this->selectedJob);
        }
    }

    public function remove()
    {
        $this->showDetails = false;
        $this->selectedJob = null;
        $this->jobs = JobListing::where('status', 'Active')->latest()->get();
    }

    // Manual search trigger
    public function searchJobs()
    {
        $this->filterJobs();
    }

    // Shared filtering logic
    private function filterJobs()
    {
        $this->jobs = JobListing::where('status', 'Active')
            ->where(function ($query) {
                $query->where('position', 'like', "%{$this->search}%")
                      ->orWhere('type', 'like', "%{$this->search}%")
                      ->orWhere('arrangement', 'like', "%{$this->search}%")
                      ->orWhere('description', 'like', "%{$this->search}%");
            })
            ->latest()
            ->get();
    }

    public function render()
    {
        // Live search happens automatically
        if ($this->search !== '') {
            $this->filterJobs();
        } else {
            $this->jobs = JobListing::where('status', 'Active')->latest()->get();
        }

        return view('livewire.website.careers', [
            'jobs' => $this->jobs,
            'selectedJob' => $this->selectedJob,
            'showDetails' => $this->showDetails
        ])->layout('layouts.website');
    }
}