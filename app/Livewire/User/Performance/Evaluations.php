<?php

namespace App\Livewire\User\Performance;

use App\Models\Performance\Evaluation;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Http;

class Evaluations extends Component
{

    public $showModal = false;
    public $editing = false;
    public $evaluationId = null;

    // Form fields
    public $employeeName = '';
    public $email = '';
    public $evaluationDate = '';
    public $evaluatorName = '';
    public $position = '';
    public $department = '';
    public $employmentDate = '';
    public $evaluationType = 'Regular';
    public $overallScore = 50;
    public $jobKnowledge = 50;
    public $workQuality = 50;
    public $initiative = 50;
    public $communication = 50;
    public $dependability = 50;
    public $attendance = 50;
    public $strengths = '';
    public $areasForImprovement = '';
    public $comments = '';
    public $status = 'Pending';

    // Employee search
    public $employees = [];
    public $filteredEmployees = [];
    public $showEmployeeDropdown = false;


    protected $rules = [
        'employeeName' => 'required|string|max:255',
        'email' => 'required|email',
        'evaluationDate' => 'required|date',
        'evaluatorName' => 'required|string|max:255',
        'position' => 'nullable|string|max:255',
        'department' => 'nullable|string|max:255',
        'employmentDate' => 'nullable|date',
        'evaluationType' => 'required|string|in:Regular,Probationary,Annual',
        'overallScore' => 'required|integer|min:0|max:100',
        'jobKnowledge' => 'required|integer|min:0|max:100',
        'workQuality' => 'required|integer|min:0|max:100',
        'initiative' => 'required|integer|min:0|max:100',
        'communication' => 'required|integer|min:0|max:100',
        'dependability' => 'required|integer|min:0|max:100',
        'attendance' => 'required|integer|min:0|max:100',
        'strengths' => 'nullable|string',
        'areasForImprovement' => 'nullable|string',
        'comments' => 'nullable|string',
        'status' => 'required|in:Pending,In Progress,Completed,Cancelled',
    ];

    public function mount()
    {
        // Fetch employees from API
        $response = Http::get('http://hr4.jetlougetravels-ph.com/api/employees');

        if ($response->successful() && is_array($response->json())) {
            $this->employees = $response->json();
            $this->filteredEmployees = $this->employees;
        } else {
            $this->employees = [];
            $this->filteredEmployees = [];
        }
    }


    public function updatedEmployeeName()
    {
        if (empty($this->employeeName)) {
            $this->filteredEmployees = $this->employees;
            $this->showEmployeeDropdown = false;
            return;
        }

        $searchTerm = strtolower($this->employeeName);
        $this->filteredEmployees = collect($this->employees)
            ->filter(function ($employee) use ($searchTerm) {
                $name = strtolower($employee['name'] ?? $employee['employee_name'] ?? '');
                return str_contains($name, $searchTerm);
            })
            ->take(10)
            ->values()
            ->toArray();
        
        $this->showEmployeeDropdown = true;
    }

    public function selectEmployee($employeeName)
    {
        $this->employeeName = $employeeName;
        
        $employee = collect($this->employees)->first(function ($emp) use ($employeeName) {
            $name = $emp['name'] ?? $emp['employee_name'] ?? '';
            return $name === $employeeName;
        });
        
        $this->email = $employee['email'] ?? null;
        
        $this->showEmployeeDropdown = false;
        $this->filteredEmployees = $this->employees;
    }

    public function openModal()
    {
        $this->reset([
            'employeeName', 'email', 'evaluationDate', 'evaluatorName',
            'position', 'department', 'employmentDate', 'evaluationType',
            'overallScore', 'jobKnowledge', 'workQuality',
            'initiative', 'communication', 'dependability', 'attendance',
            'strengths', 'areasForImprovement', 'comments',
            'status', 'editing', 'evaluationId'
        ]);
        $this->showModal = true;
    }

    // Calculate overall score when performance ratings change
    public function updatedJobKnowledge()
    {
        $this->calculateOverallScore();
    }

    public function updatedWorkQuality()
    {
        $this->calculateOverallScore();
    }

    public function updatedInitiative()
    {
        $this->calculateOverallScore();
    }

    public function updatedCommunication()
    {
        $this->calculateOverallScore();
    }

    public function updatedDependability()
    {
        $this->calculateOverallScore();
    }

    public function updatedAttendance()
    {
        $this->calculateOverallScore();
    }

    public function calculateOverallScore()
    {
        $ratings = [
            $this->jobKnowledge,
            $this->workQuality,
            $this->initiative,
            $this->communication,
            $this->dependability,
            $this->attendance
        ];

        // Calculate average of all ratings (they should all have values)
        if (count($ratings) > 0) {
            $this->overallScore = round(array_sum($ratings) / count($ratings));
        } else {
            $this->overallScore = 50;
        }
    }

    public function editEvaluation($id)
    {
        $evaluation = Evaluation::find($id);
        
        if ($evaluation) {
            $this->evaluationId = $id;
            $this->employeeName = $evaluation->employee_name;
            $this->email = $evaluation->email;
            $this->evaluationDate = $evaluation->evaluation_date->format('Y-m-d');
            $this->evaluatorName = $evaluation->evaluator_name;
            $this->performanceAreas = $evaluation->performance_areas;
            $this->overallScore = $evaluation->overall_score;
            $this->strengths = $evaluation->strengths;
            $this->areasForImprovement = $evaluation->areas_for_improvement;
            $this->comments = $evaluation->comments;
            $this->status = $evaluation->status;
            $this->editing = true;
            $this->showModal = true;
        }
    }

    public function addEvaluation()
    {
        $this->validate();

        Evaluation::create([
            'employee_name' => $this->employeeName,
            'email' => $this->email,
            'evaluation_date' => $this->evaluationDate,
            'evaluator_name' => $this->evaluatorName,
            'position' => $this->position,
            'department' => $this->department,
            'employment_date' => $this->employmentDate,
            'evaluation_type' => $this->evaluationType,
            'overall_score' => $this->overallScore,
            'job_knowledge' => $this->jobKnowledge,
            'work_quality' => $this->workQuality,
            'initiative' => $this->initiative,
            'communication' => $this->communication,
            'dependability' => $this->dependability,
            'attendance' => $this->attendance,
            'strengths' => $this->strengths,
            'areas_for_improvement' => $this->areasForImprovement,
            'comments' => $this->comments,
            'employee_comments' => '',
            'status' => $this->status,
        ]);

        $this->dispatch('evaluation-added', 'Evaluation scheduled successfully!');
        $this->showModal = false;
    }

    public function updateEvaluation()
    {
        $this->validate();

        $evaluation = Evaluation::find($this->evaluationId);
        
        if ($evaluation) {
            $evaluation->update([
                'employee_name' => $this->employeeName,
                'email' => $this->email,
                'evaluation_date' => $this->evaluationDate,
                'evaluator_name' => $this->evaluatorName,
                'overall_score' => $this->overallScore,
                'strengths' => $this->strengths,
                'areas_for_improvement' => $this->areasForImprovement,
                'comments' => $this->comments,
                'status' => $this->status,
            ]);

            $this->dispatch('evaluation-updated', 'Evaluation updated successfully!');
            $this->showModal = false;
        }
    }

    public function deleteEvaluation($id)
    {
        $evaluation = Evaluation::find($id);
        
        if ($evaluation) {
            $evaluation->delete();
            $this->dispatch('evaluation-deleted', 'Evaluation deleted successfully!');
        }
    }


    public function render()
    {
        return view('livewire.user.performance.evaluations')->layout('layouts.app');
    }
}
