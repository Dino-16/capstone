<?php

namespace App\Livewire\User;

use Livewire\Component;
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
        $this->initializePasswordVerification();
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

    public function exportRequisition()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new RequisitionsExport(), 'requisitions.xlsx');
    }
    
    public function exportJobPost()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new JobPostsExport(), 'job_posts.xlsx');
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
        
        return \Maatwebsite\Excel\Facades\Excel::download(new EmployeesExport($employees), 'employees.xlsx');
    }
    
    public function exportDocumentChecklist()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new DocumentChecklistsExport(), 'document_checklists.xlsx');
    }
    
    public function exportOrientationSchedule()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new OrientationSchedulesExport(), 'orientation_schedules.xlsx');
    }
    
    public function exportEvaluationRecords()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new EvaluationRecordsExport(), 'evaluation_records.xlsx');
    }
    
    public function exportRewards()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new RewardsExport(), 'rewards.xlsx');
    }
    
    public function exportGiveRewards()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new GiveRewardsExport(), 'give_rewards.xlsx');
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
            'report_file' => $this->reportName . '_' . date('Y-m-d_H-i-s') . '.xlsx',
            'status' => 'published',
        ]);

        $this->reset(['reportName', 'reportType']);
        $this->reports = Report::latest()->get();
        
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
            $this->reports = Report::latest()->get();
        }
    }

    public function render()
    {
        return view('livewire.user.reports')->layout('layouts.app');
    }
}
