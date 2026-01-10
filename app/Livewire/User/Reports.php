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
        $export = new RequisitionsExport();
        return $export->export();
    }
    
    public function exportJobPost()
    {
        $export = new JobPostsExport();
        return $export->export();
    }
    
    public function exportEmployee()
    {
        $export = new EmployeesExport();
        return $export->export();
    }
    
    public function exportDocumentChecklist()
    {
        $export = new DocumentChecklistExport();
        return $export->export();
    }
    
    public function exportOrientationSchedule()
    {
        $export = new OrientationScheduleExport();
        return $export->export();
    }
    
    public function exportEvaluationRecords()
    {
        $export = new EvaluationRecordsExport();
        return $export->export();
    }
    
    public function exportRewards()
    {
        $export = new RewardsExport();
        return $export->export();
    }
    
    public function exportGiveRewards()
    {
        $export = new GiveRewardsExport();
        return $export->export();
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
