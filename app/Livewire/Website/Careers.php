<?php

namespace App\Livewire\Website;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Recruitment\JobListing;

class Careers extends Component
{
    use WithPagination;

    public $showDetails = false;
    public $selectedJob;
    public $search = '';

    protected $paginationTheme = 'bootstrap';

    public function viewDetails($id)
    {
        $this->selectedJob = JobListing::where('status', 'Active')->find($id);
        $this->showDetails = true;
    }

    public function remove()
    {
        $this->showDetails = false;
        $this->selectedJob = null;
    }

    // Manual search trigger
    public function searchJobs()
    {
        $this->resetPage();
    }

    // Reset pagination when search changes
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = JobListing::where('status', 'Active');

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('position', 'like', "%{$this->search}%")
                  ->orWhere('type', 'like', "%{$this->search}%")
                  ->orWhere('arrangement', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%");
            });
        }

        $jobs = $query->latest()->paginate(6);

        return view('livewire.website.careers', [
            'jobs' => $jobs,
            'selectedJob' => $this->selectedJob,
            'showDetails' => $this->showDetails
        ])->layout('layouts.website');
    }
}