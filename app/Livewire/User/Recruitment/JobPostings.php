<?php

namespace App\Livewire\User\Recruitment;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\Recruitment\Requisition;
use App\Models\Recruitment\JobListing;
use App\Exports\Recruitment\JobPostsExport;
use Illuminate\Support\Facades\Http;


class JobPostings extends Component
{
    use WithPagination;
    use \App\Livewire\Traits\HandlesToasts;

    public $showModal = false;
    public $jobDetail;
    public $search;

    #[Url]
    public $jobListSort = 'position_asc';

    public $filterMonth;
    public $departmentFilter = '';
    public $positionFilter = '';

    public $type = 'On-Site';
    public $arrangement = 'Full-Time';
    public $expiration_date;
    
    // API data
    public $apiPositions = [];
    public $debugApiData = null; 

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

        $this->toast('Job details updated successfully.');
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

        $this->toast('Job successfully published.');
        $this->closeModal();
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

    public function updatedDepartmentFilter()
    {
        $this->resetPage();
    }

    public function updatedPositionFilter()
    {
        $this->resetPage();
    }

    public function export()
    {
        return (new JobPostsExport)->download('job_posts.csv');
    }


    public $showDeleteModal = false;
    public $jobIdToDelete = null;

    public function confirmDelete($jobId)
    {
        $this->jobIdToDelete = $jobId;
        $this->showDeleteModal = true;
    }

    public function deactivateJob()
    {
        if (!in_array(session('user.position'), ['Super Admin', 'HR Manager'])) {
            $this->toast('Unauthorized action.', 'error');
            return;
        }

        if ($this->jobIdToDelete) {
            $job = JobListing::findOrFail($this->jobIdToDelete);
            $positionName = $job->position;
            $job->delete();
    
            // Update requisition opening if exists
            $requisition = Requisition::where('position', $positionName)->first();
            if ($requisition) {
                $requisition->increment('opening');
            }
    
            $this->toast('Job post removed successfully!');
        }

        $this->showDeleteModal = false;
        $this->jobIdToDelete = null;
    }


    public function editJob($jobId)
    {
        $this->jobDetail = JobListing::findOrFail($jobId);
        
        $this->type = $this->jobDetail->type ?? 'On-Site';
        $this->arrangement = $this->jobDetail->arrangement ?? 'Full-Time';
        $this->expiration_date = $this->jobDetail->expiration_date;
        
        $this->showModal = true;
    }

    public function saveJobEdit()
    {
        $this->validate([
            'expiration_date' => 'required|date|after_or_equal:today',
        ]);

        $this->jobDetail->update([
            'type'            => $this->type,
            'arrangement'     => $this->arrangement,
            'expiration_date' => $this->expiration_date,
        ]);

        $this->toast('Job "' . $this->jobDetail->position . '" updated successfully.');
        $this->closeModal();
    }

    public function activateApiPosition($positionData)
    {
        // Check if position already exists
        $existingJob = JobListing::where('position', $positionData['position_name'])->first();
        
        if ($existingJob) {
            // Update existing job to Active
            $existingJob->update([
                'status' => 'Active',
                'expiration_date' => now()->addDays(30), // Default 30 days
            ]);
            $this->toast('Job "' . $positionData['position_name'] . '" is already listed and has been activated.');
        } else {
            // Create new job listing from API data
            JobListing::create([
                'position' => $positionData['position_name'],
                'description' => $positionData['description'] ?? '',
                'qualifications' => $positionData['qualification'] ?? '',
                'department' => $positionData['department'] ?? 'N/A',
                'type' => $this->mapWorkArrangement($positionData['work_arrangement'] ?? 'On-site'),
                'arrangement' => $positionData['employment_type'] ?? 'Full-Time',
                'location' => 'Ever Gotesco Commonwealth',
                'status' => 'Active',
                'expiration_date' => now()->addDays(30), // Default 30 days expiration
            ]);
            $this->toast('Job "' . $positionData['position_name'] . '" has been activated successfully!');
        }
    }

    private function mapWorkArrangement($workArrangement)
    {
        if (str_contains(strtolower($workArrangement), 'remote')) {
            return 'Remote';
        } elseif (str_contains(strtolower($workArrangement), 'hybrid')) {
            return 'Hybrid';
        }
        return 'On-Site';
    }

    public function render()
    {
        $requisitions = Requisition::where('status', 'Accepted')->get();

        // Fetch positions from HR2 API
        try {
            $response = Http::withoutVerifying()->get('https://hr2.jetlougetravels-ph.com/api/positions');
            
            if ($response->successful()) {
                $this->apiPositions = $response->json();
                $this->debugApiData = $this->apiPositions; // For debugging
            }
        } catch (\Exception $e) {
            $this->apiPositions = [];
            $this->debugApiData = ['error' => $e->getMessage()];
        }

        // Apply sorting to jobs query
        $query = JobListing::query();

        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('position', 'like', '%' . $this->search . '%')
                ->orWhere('type', 'like', '%' . $this->search . '%')
                ->orWhere('arrangement', 'like', '%' . $this->search . '%');
            });
        }

        if (!empty($this->filterMonth)) {
            $query->whereMonth('expiration_date', $this->filterMonth);
        }

        if ($this->departmentFilter) {
            $query->where('department', $this->departmentFilter);
        }

        if ($this->positionFilter) {
            $query->where('position', $this->positionFilter);
        }
        
        // Get distinct departments and positions for filters
        $departments = JobListing::where('status', 'Active')->select('department')->distinct()->orderBy('department')->pluck('department');
        $positions = JobListing::where('status', 'Active')->select('position')->distinct()->orderBy('position')->pluck('position');

        $jobs = $query->where('status', 'Active')->orderBy('expiration_date', 'desc')->paginate(10);

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
            'apiPositions' => $this->apiPositions,
            'debugApiData' => $this->debugApiData,
            'departments'  => $departments,
            'positions'    => $positions,
        ])->layout('layouts.app');
    }
}
