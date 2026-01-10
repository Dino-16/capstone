<?php

namespace App\Livewire\User\Recruitment;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\Recruitment\Requisition;
use App\Models\Recruitment\JobListing;
use App\Exports\Recruitment\JobPostsExport;

class JobPostings extends Component
{
    use WithPagination;

    public $showModal = false;
    public $jobDetail;
    public $search;

    #[Url(keep: true)]
    public $jobListSort = 'position_asc';

    public $type = 'On-Site';
    public $arrangement = 'Full-Time';
    public $expiration_date; 

    public function showJobDetails($id)
    {
        $this->jobDetail = JobListing::findOrFail($id);

        $this->type = $this->jobDetail->type ?? 'On-Site';
        $this->arrangement = $this->jobDetail->arrangement ?? 'Full-Time';
        // 2. Preload value if it exists
        $this->expiration_date = $this->jobDetail->expiration_date; 
        
        $this->showModal = true;
    }

    public function createJobFromRequisition($id)
    {
        $requisition = Requisition::findOrFail($id);
        
        $jobDetail = JobListing::create([
            'position' => $requisition->position,
            'department' => $requisition->department,
            'description' => $requisition->description ?? '',
            'qualifications' => $requisition->qualifications ?? '',
            'type' => 'On-Site',
            'arrangement' => 'Full-Time',
            'status' => 'Inactive',
            'expiration_date' => null,
        ]);

        $this->showJobDetails($jobDetail->id);
    }

    public function updateJob()
    {
        $this->jobDetail->update([
            'type'            => $this->type,
            'arrangement'     => $this->arrangement,
            'expiration_date' => $this->expiration_date, 
        ]);

        session()->push('status', 'Job details updated successfully.');
        $this->closeModal();
    }

    public function publishJob()
    {
        $this->validate([
            'expiration_date' => 'required|date|after_or_equal:today', 
        ]);

        $this->jobDetail->update([
            'type'            => $this->type,
            'arrangement'     => $this->arrangement,
            'expiration_date' => $this->expiration_date, 
            'status'          => 'Active', 
        ]);

        session()->push('status', 'Job successfully published.');
        $this->closeModal();
    }

        // Clear Message Status
    public function clearStatus()
    {
        session()->forget('status');
    }
    
    public function closeModal()
    {
        $this->showModal = false;
        $this->jobDetail = null;
        $this->reset(['type', 'arrangement', 'expiration_date']); 
    }

    public function updatingJobListSort()
    {
        $this->resetPage();
    }

    public function clearJobListFilter()
    {
        $this->jobListSort = 'position_asc';
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function export()
    {
        $export = new JobPostsExport();
        return $export->export();
    }


    public function deactivateJob($jobId)
    {
        $job = JobListing::findOrFail($jobId);
        $job->update([
            'status' => 'Inactive',
            'expiration_date' => null
        ]);
        
        session()->push('status', 'Job deactivated successfully.');
    }

    public function render()
    {
        $requisitions = Requisition::where('status', 'Accepted')->get();

        // Apply sorting to jobs query
        $query = JobListing::query();

        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('position', 'like', '%' . $this->search . '%')
                ->orWhere('type', 'like', '%' . $this->search . '%')
                ->orWhere('arrangement', 'like', '%' . $this->search . '%');
            });
        }

        $jobs = $query->paginate(10);

        // Get all jobs for the sidebar card with its own sort
        $sidebarJobsQuery = JobListing::query();
        
        [$column, $direction] = explode('_', $this->jobListSort);
        
        // Map column names to actual database columns
        $columnMap = [
            'position' => 'position',
            'updated_at' => 'updated_at',
            'expiration_date' => 'expiration_date',
        ];

        $column = $columnMap[$column] ?? 'position';
        $sidebarJobsQuery->orderBy($column, $direction);
        
        $sidebarJobs = $sidebarJobsQuery->get();

        return view('livewire.user.recruitment.job-postings', [
            'requisitions' => $requisitions,
            'jobs'         => $jobs,
            'sidebarJobs'  => $sidebarJobs,
        ])->layout('layouts.app');
    }
}
