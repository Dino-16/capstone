<?php

namespace App\Livewire\User;

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Exports\Recruitment\RequisitionsExport;
use App\Exports\Recruitment\JobPostsExport;
use App\Exports\Onboarding\EmployeesExport;
use App\Exports\Onboarding\DocumentChecklistsExport;
use App\Exports\Onboarding\OrientationSchedulesExport;
use App\Exports\Performance\EvaluationRecordsExport;
use App\Exports\Recognition\RewardsExport;
use App\Exports\Recognition\GiveRewardsExport;
use App\Models\Report;
use App\Models\Onboarding\DocumentChecklist;
use App\Livewire\Traits\RequiresPasswordVerification;

class Reports extends Component
{
    use \App\Livewire\Traits\HandlesToasts;
    use RequiresPasswordVerification;

    public $reportName;
    public $reportType;
    public $search = '';
    public $typeFilter = 'All';

    public $reportTypes = [
        'requisition' => 'Requisitions',
        'jobpost' => 'Job Posts',
        'employee' => 'Employees',
        'documentchecklist' => 'Document Checklists',
        'orientationschedule' => 'Orientation Schedules',
        'evaluationrecords' => 'Evaluation Records',
        'rewards' => 'Rewards',
        'giverewards' => 'Give Rewards',
    ];

    public function mount()
    {
        $this->initializePasswordVerification();
    }

    #[Computed]
    public function filteredReports()
    {
        return Report::query()
            ->when($this->search, function ($query) {
                $query->where('report_name', 'like', '%' . $this->search . '%');
            })
            ->when($this->typeFilter !== 'All', function ($query) {
                $query->where('report_type', $this->typeFilter);
            })
            ->latest()
            ->get();
    }

    public function exportRequisition()
    {
        return (new RequisitionsExport())->download('requisitions.csv');
    }
    
    public function exportJobPost()
    {
        return (new JobPostsExport())->download('job_posts.csv');
    }
    
    public function exportEmployee()
    {
        $employees = DocumentChecklist::get()->map(function ($checklist) {
            return [
                'Name' => $checklist->employee_name,
                'Position' => 'N/A',
                'Department' => 'N/A',
                'Contract Signing' => $checklist->getDocumentStatus('resume'),
                'HR Documents' => $checklist->getDocumentStatus('medical_certificate'),
                'Training Modules' => $checklist->getDocumentStatus('valid_government_id'),
            ];
        })->toArray();
        
        return (new EmployeesExport($employees))->download('employees.csv');
    }
    
    public function exportDocumentChecklist()
    {
        return (new DocumentChecklistsExport())->download('document_checklists.csv');
    }
    
    public function exportOrientationSchedule()
    {
        return (new OrientationSchedulesExport())->download('orientation_schedules.csv');
    }
    
    public function exportEvaluationRecords()
    {
        return (new EvaluationRecordsExport())->download('evaluation_records.csv');
    }
    
    public function exportRewards()
    {
        return (new RewardsExport())->download('rewards.csv');
    }
    
    public function exportGiveRewards()
    {
        return (new GiveRewardsExport())->download('give_rewards.csv');
    }

    /**
     * Quick generate/export a report by type
     */
    public function quickGenerate($type)
    {
        return match ($type) {
            'requisition' => $this->exportRequisition(),
            'jobpost' => $this->exportJobPost(),
            'employee' => $this->exportEmployee(),
            'documentchecklist' => $this->exportDocumentChecklist(),
            'orientationschedule' => $this->exportOrientationSchedule(),
            'evaluationrecords' => $this->exportEvaluationRecords(),
            'rewards' => $this->exportRewards(),
            'giverewards' => $this->exportGiveRewards(),
            default => $this->toast('Unknown report type.'),
        };
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
            'report_file' => $this->reportName . '_' . date('Y-m-d_H-i-s') . '.csv',
            'status' => 'published',
        ]);

        $this->reset(['reportName', 'reportType']);
        unset($this->filteredReports);
        
        $this->toast('Report created successfully! Click the download button to generate the file.');
    }

    public function deleteReport($id)
    {
        if (auth()->user()->role !== 'Super Admin') {
            $this->toast('Unauthorized action.');
            return;
        }

        $report = Report::find($id);
        if ($report) {
            $report->delete();
            $this->toast('Report deleted successfully!');
            unset($this->filteredReports);
        }
    }

    public function render()
    {
        return view('livewire.user.reports')->layout('layouts.app');
    }
}
