<?php

namespace App\Livewire\User\Performance;

use App\Models\Performance\Evaluation;
use Livewire\Component;
use Livewire\WithPagination;

class EvaluationRecords extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $scoreFilter = '';
    public $dateFilter = '';
    public $showDrafts = false;
    
    // Modal properties
    public $showEditModal = false;
    public $editingEvaluationId = null;
    public $employeeName;
    public $email;
    public $evaluationDate;
    public $evaluatorName;
    public $overallScore;
    public $performanceAreas;
    public $notes;
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

    public function updatingDateFilter()
    {
        $this->resetPage();
    }

    public function exportData()
    {
        $evaluations = Evaluation::all();
        
        $csvContent = "Employee Name,Email,Evaluation Date,Evaluator,Overall Score,Status,Performance Areas,Created At\n";
        
        foreach ($evaluations as $evaluation) {
            $csvContent .= '"' . $evaluation->employee_name . '",';
            $csvContent .= '"' . $evaluation->email . '",';
            $csvContent .= '"' . $evaluation->evaluation_date->format('Y-m-d') . '",';
            $csvContent .= '"' . $evaluation->evaluator_name . '",';
            $csvContent .= $evaluation->overall_score . ',';
            $csvContent .= '"' . $evaluation->status . '",';
            $csvContent .= '"' . str_replace('"', '""', $evaluation->performance_areas) . '",';
            $csvContent .= '"' . $evaluation->created_at->format('Y-m-d H:i:s') . '"' . "\n";
        }
        
        $filename = "evaluation_records_" . date('Y-m-d_H-i-s') . ".csv";
        
        return response()->streamDownload(function () use ($csvContent) {
            echo $csvContent;
        }, $filename);
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
                'performanceAreas' => 'required|string',
                'status' => 'required|in:Draft,Pending,Completed',
            ]);

            $evaluation = Evaluation::findOrFail($this->editingEvaluationId);
            
            $evaluation->update([
                'employee_name' => $this->employeeName,
                'email' => $this->email,
                'evaluation_date' => $this->evaluationDate,
                'evaluator_name' => $this->evaluatorName,
                'overall_score' => $this->overallScore,
                'performance_areas' => $this->performanceAreas,
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
        $evaluation->status = 'Pending';
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

        // Filter by date
        if ($this->dateFilter) {
            switch ($this->dateFilter) {
                case 'today':
                    $query->whereDate('evaluation_date', today());
                    break;
                case 'week':
                    $query->whereBetween('evaluation_date', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('evaluation_date', now()->month)
                          ->whereYear('evaluation_date', now()->year);
                    break;
                case 'year':
                    $query->whereYear('evaluation_date', now()->year);
                    break;
            }
        }

        $evaluations = $query->latest('evaluation_date')->paginate(10);

        // Get statistics
        $stats = [
            'total' => Evaluation::count(),
            'pending' => Evaluation::where('status', 'Pending')->count(),
            'completed' => Evaluation::where('status', 'Completed')->count(),
            'draft' => Evaluation::where('status', 'Draft')->count(),
            'average_score' => Evaluation::avg('overall_score'),
            'this_month' => Evaluation::whereMonth('evaluation_date', now()->month)
                                   ->whereYear('evaluation_date', now()->year)
                                   ->count(),
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
