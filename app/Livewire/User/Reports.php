<?php

namespace App\Livewire\User;

use Livewire\Component;
use App\Exports\Recruitment\RequisitionsExport;
use App\Exports\Recruitmnet\JobPostsExport;
use App\Exports\Onboarding\EmployeesExport;
use App\Exports\Onboarding\DocumentChecklistExport;
use App\Onboarding\OrientationScheduleExport;
use App\Performance\EvaluationRecordsExport;
use App\Recognition\RewardsExport;
use App\Recognition\GiveRewardsExport;
use App\Models\Report;

class Reports extends Component
{

    public $reportName;
    public $reportType;
    public $reports;
    public $search = '';
    public $typeFilter = 'All';

    public $reportTypes = [
        'requisition' => 'Requisition',
        'jobpost' => 'Job Post',
        'employee' => 'Employee',
        'documentchecklist' => 'Document Checklist',
        'orientationschedule' => 'Orientation Schedule',
        'evaluationrecords' => 'Evaluation Records',
        'rewards' => 'Rewards',
        'giverewards' => 'Give Rewards',
    ];

    public function mount()
    {
        $this->reports = Report::latest()->get();
    }

    public function getFilteredReportsProperty()
    {
        $query = Report::latest();

        // Apply search filter
        if ($this->search) {
            $query->where('report_name', 'like', '%' . $this->search . '%');
        }

        // Apply type filter
        if ($this->typeFilter !== 'All') {
            $query->where('report_type', $this->typeFilter);
        }

        return $query->get();
    }

    public function getReportTypeCountsProperty()
    {
        $counts = [];
        
        // Count all reports
        $counts['All'] = Report::count();
        
        // Count by each report type
        foreach ($this->reportTypes as $type => $label) {
            $counts[$label] = Report::where('report_type', $type)->count();
        }
        
        return $counts;
    }

    public function exportRequisition()
    {
        return (new RequisitionsExport)->download('requisitions.xlsx');
    }
    
    public function exportJobPost()
    {
        return (new JobPostsExport)->download('job_posts.xlsx');
    }
    
    public function exportEmployee()
    {
        return (new EmployeesExport)->download('employees.xlsx');
    }
    
    public function exportDocumentChecklist()
    {
        return (new DocumentChecklistExport)->download('document_checklist.xlsx');
    }
    
    public function exportOrientationSchedule()
    {
        return (new OrientationScheduleExport)->download('orientation_schedule.xlsx');
    }
    
    public function exportEvaluationRecords()
    {
        return (new EvaluationRecordsExport)->download('evaluation_records.xlsx');
    }
    
    public function exportRewards()
    {
        return (new RewardsExport)->download('rewards.xlsx');
    }
    
    public function exportGiveRewards()
    {
        return (new GiveRewardsExport)->download('give_rewards.xlsx');
    }
    
    public function saveReport()
    {
        $this->validate([
            'reportName' => 'required|string|max:255',
            'reportType' => 'required|in:' . implode(',', array_keys($this->reportTypes)),
        ]);

        Report::create([
            'report_name' => $this->reportName,
            'report_type' => $this->reportType,
            'report_file' => 'xlsx',
            'status' => 'published',
        ]);

        $this->reset(['reportName', 'reportType']);
        $this->reports = Report::latest()->get();
        
        // Close modal and show success message
        $this->dispatch('closeModal');
        session()->flash('message', 'Report created successfully!');
    }

    public function render()
    {
        return view('livewire.user.reports')->layout('layouts.app');
    }
}
