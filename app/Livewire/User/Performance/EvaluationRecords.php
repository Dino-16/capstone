<?php

namespace App\Livewire\User\Performance;

use App\Models\Performance\Evaluation;
use App\Exports\Performance\EvaluationRecordsExport;
use Livewire\Component;
use Livewire\WithPagination;

class EvaluationRecords extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $scoreFilter = '';
    public $showDrafts = false;
    
    // Modal properties
    public $showEditModal = false;
    public $showViewModal = false;
    public $editingEvaluationId = null;
    public $viewingEvaluation = null;
    public $employeeName;
    public $email;
    public $evaluationDate;
    public $evaluatorName;
    public $overallScore;
    public $performanceAreas;
    public $notes;
    public $employmentDate;
    public $status = 'Pending';

    protected $paginationTheme = 'bootstrap';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingScoreFilter()
    {
        $this->resetPage();
    }


    public function exportData()
    {
        $export = new EvaluationRecordsExport();
        return $export->export();
    }

    public function viewEvaluation($id)
    {
        $this->viewingEvaluation = Evaluation::findOrFail($id);
        $this->showViewModal = true;
    }

    public function editEvaluation($id)
    {
        $evaluation = Evaluation::findOrFail($id);
        
        $this->editingEvaluationId = $id;
        $this->employeeName = $evaluation->employee_name;
        $this->email = $evaluation->email;
        $this->evaluationDate = $evaluation->evaluation_date->format('Y-m-d\TH:i');
        $this->evaluatorName = $evaluation->evaluator_name;
        $this->overallScore = $evaluation->overall_score;
        $this->performanceAreas = $evaluation->performance_areas;
        $this->notes = $evaluation->notes;
        $this->employmentDate = $evaluation->employment_date ? $evaluation->employment_date->format('Y-m-d') : null;
        $this->status = $evaluation->status;
        $this->showEditModal = true;
    }

    public function updateEvaluation()
    {
        try {
            $this->validate([
                'employeeName' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
                'evaluationDate' => 'required|date',
                'evaluatorName' => 'required|string|max:255',
                'overallScore' => 'required|numeric|min:0|max:100',
                'employmentDate' => 'nullable|date',
                'performanceAreas' => 'required|string',
                'status' => 'required|in:Draft,Ongoing,Completed',
            ]);

            $evaluation = Evaluation::findOrFail($this->editingEvaluationId);
            
            $evaluation->update([
                'employee_name' => $this->employeeName,
                'email' => $this->email,
                'evaluation_date' => $this->evaluationDate,
                'evaluator_name' => $this->evaluatorName,
                'overall_score' => $this->overallScore,
                'performance_areas' => $this->performanceAreas,
                'employment_date' => $this->employmentDate,
                'notes' => $this->notes,
                'status' => $this->status,
            ]);

            session()->flash('message', 'Evaluation updated successfully!');
            $this->showEditModal = false;
            $this->reset(['employeeName', 'email', 'evaluationDate', 'evaluatorName', 'overallScore', 'performanceAreas', 'notes', 'status', 'editingEvaluationId']);
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error updating evaluation: ' . $e->getMessage());
        }
    }

    public function deleteEvaluation($id)
    {
        if (auth()->user()->role !== 'Super Admin') {
            session()->flash('error', 'Unauthorized action.');
            return;
        }

        $evaluation = Evaluation::find($id);
        
        if ($evaluation) {
            $evaluation->delete();
            session()->flash('message', 'Evaluation deleted successfully!');
        }
    }

    // Draft functionality
    public function draft($id)
    {
        $evaluation = Evaluation::findOrFail($id);
        $evaluation->status = 'Draft';
        $evaluation->save();
        session()->flash('message', 'Evaluation drafted successfully!');
    }

    public function restore($id) 
    {
        $evaluation = Evaluation::findOrFail($id);
        $evaluation->status = 'Ongoing';
        $evaluation->save();    
        session()->flash('message', 'Draft restored successfully!');
    }

    public function openDraft()
    {
        $this->showDrafts = true;
        $this->resetPage();
    }

    public function showAll()
    {
        $this->showDrafts = false;
        $this->resetPage();
    }

    public function render()
    {
        $query = Evaluation::query();

        // Search by employee name or email
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('employee_name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('evaluator_name', 'like', '%' . $this->search . '%');
            });
        }

        // Filter by status
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        // Filter by score range
        if ($this->scoreFilter) {
            switch ($this->scoreFilter) {
                case 'excellent':
                    $query->where('overall_score', '>=', 90);
                    break;
                case 'good':
                    $query->whereBetween('overall_score', [70, 89]);
                    break;
                case 'average':
                    $query->whereBetween('overall_score', [50, 69]);
                    break;
                case 'poor':
                    $query->where('overall_score', '<', 50);
                    break;
            }
        }


        $evaluations = $query->latest('evaluation_date')->paginate(10);

        // Add evaluation count per employee
        foreach ($evaluations as $evaluation) {
            $evaluation->employee_evaluation_count = Evaluation::where('employee_name', $evaluation->employee_name)->count();
            $evaluation->employee_completed_count = Evaluation::where('employee_name', $evaluation->employee_name)
                ->where('status', 'Completed')->count();
        }

        // Get statistics
        $stats = [
            'total' => Evaluation::count(),
            'ongoing' => Evaluation::where('status', 'Ongoing')->count(),
            'completed' => Evaluation::where('status', 'Completed')->count(),
            'draft' => Evaluation::where('status', 'Draft')->count(),
        ];

        if ($this->showDrafts) {
            $drafts = Evaluation::where('status', 'Draft')
                        ->latest('evaluation_date')
                        ->paginate(10);

            return view('livewire.user.performance.evaluation-records', [
                'evaluations' => null,
                'drafts' => $drafts,
                'stats' => $stats
            ])->layout('layouts.app');
        }

        return view('livewire.user.performance.evaluation-records', [
            'evaluations' => $evaluations,
            'drafts' => null,
            'stats' => $stats
        ])->layout('layouts.app');
    }
}
