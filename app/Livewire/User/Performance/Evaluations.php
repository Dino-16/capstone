<?php

namespace App\Livewire\User\Performance;

use App\Models\Performance\Evaluation;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Http;

class Evaluations extends Component
{

    // Form fields
    public $employeeName = '';
    public $email = '';
    public $evaluationDate = '2026-01-15';
    public $evaluatorName = 'HR Manager';
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
    public $status = 'Draft';

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
        'status' => 'required|in:Draft,Ongoing,Completed',
    ];

    public function mount()
    {
        // Initialize empty - employees will be fetched on search
        $this->employees = [];
        $this->filteredEmployees = [];
        
        // Check for prefilled data from Tracker page
        if (session()->has('prefill_evaluation')) {
            $prefill = session()->pull('prefill_evaluation');
            $this->employeeName = $prefill['employeeName'] ?? '';
            $this->email = $prefill['email'] ?? '';
            $this->position = $prefill['position'] ?? '';
            $this->department = $prefill['department'] ?? '';
        }
    }


    public function updatedEmployeeName()
    {
        if (strlen($this->employeeName) < 2) {
            $this->employees = [];
            $this->filteredEmployees = [];
            $this->showEmployeeDropdown = false;
            return;
        }

        try {
            $response = Http::get('http://hr4.jetlougetravels-ph.com/api/employees', [
                'search' => $this->employeeName,
                'per_page' => 10
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $rawEmployees = $data['data'] ?? $data ?? [];
                
                $this->employees = collect($rawEmployees)->map(function($emp) {
                    // Normalize name for checking in selectEmployee
                    $name = $emp['full_name'] ?? trim(($emp['first_name'] ?? '') . ' ' . ($emp['last_name'] ?? ''));
                    $emp['name'] = $name;
                    $emp['employee_name'] = $name; 
                    return $emp;
                })->toArray();
                
                $this->filteredEmployees = $this->employees;
                $this->showEmployeeDropdown = count($this->employees) > 0;
            }
        } catch (\Exception $e) {
            $this->employees = [];
            $this->filteredEmployees = [];
            $this->showEmployeeDropdown = false;
        }
    }

    public function selectEmployee($employeeName)
    {
        $this->employeeName = $employeeName;
        
        $employee = collect($this->employees)->first(function ($emp) use ($employeeName) {
            $name = $emp['name'] ?? $emp['employee_name'] ?? '';
            return $name === $employeeName;
        });
        
        // Debug: Log the employee data structure
        logger('=== Employee Data Structure ===');
        logger(json_encode($employee, JSON_PRETTY_PRINT));
        
        $this->email = $employee['email'] ?? null;
        $this->position = $employee['position'] ?? null;
        
        // Handle department - could be string or object
        $department = $employee['department'] ?? null;
        if (is_array($department)) {
            $this->department = $department['name'] ?? $department['department_name'] ?? null;
            logger('Department is array: ' . json_encode($department));
        } else {
            $this->department = $department;
            logger('Department is string: ' . $department);
        }
        
        $this->showEmployeeDropdown = false;
        $this->filteredEmployees = [];
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

        session()->flash('status', 'Evaluation scheduled successfully!');
        
        $this->reset([
            'employeeName', 'email', 'evaluationDate', 'evaluatorName',
            'position', 'department', 'employmentDate', 'evaluationType',
            'overallScore', 'jobKnowledge', 'workQuality',
            'initiative', 'communication', 'dependability', 'attendance',
            'strengths', 'areasForImprovement', 'comments',
            'status'
        ]);
    }

    public function clearStatus()
    {
        session()->forget(['status', 'error']);
    }

    public function render()
    {
        return view('livewire.user.performance.evaluations')->layout('layouts.app');
    }
}
