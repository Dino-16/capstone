<?php

namespace App\Livewire\User\Applicants;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Recruitment\JobListing;
use App\Models\Applicants\Candidate;
use Illuminate\Support\Facades\Http;

class Interviews extends Component
{
    use WithPagination;

    public $search;
    public $statusFilter = '';
    
    // Interview modal properties
    public $showInterviewModal = false;
    public $selectedCandidateId = null;
    public $selectedCandidate = null;
    public $selectedPosition = '';
    
    // Scoring properties
    public $interviewScores = [];
    public $practicalScores = [];
    public $overallNotes = '';
    public $interviewQuestions = [];
    public $practicalExams = [];
    
    // Result properties
    public $showResultModal = false;
    public $resultCandidate = null;
    public $interviewResult = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    // Open the interview assessment modal
    public function openInterviewModal($candidateId)
    {
        $candidate = Candidate::find($candidateId);
        if (!$candidate) {
            session()->flash('error', 'Candidate not found.');
            return;
        }

        $this->selectedCandidateId = $candidateId;
        $this->selectedCandidate = $candidate;
        $this->selectedPosition = $candidate->applied_position ?? 'Travel Agent';
        
        // Load questions and exams for the position
        $this->interviewQuestions = $this->getInterviewQuestions($this->selectedPosition);
        $this->practicalExams = $this->getPracticalExams($this->selectedPosition);
        
        // Initialize scores
        $this->interviewScores = array_fill(0, count($this->interviewQuestions), ['answer' => '', 'score' => 0]);
        $this->practicalScores = array_fill(0, count($this->practicalExams), ['response' => '', 'score' => 0]);
        $this->overallNotes = '';
        
        $this->showInterviewModal = true;
    }

    public function closeInterviewModal()
    {
        $this->showInterviewModal = false;
        $this->selectedCandidateId = null;
        $this->selectedCandidate = null;
        $this->selectedPosition = '';
        $this->interviewScores = [];
        $this->practicalScores = [];
        $this->overallNotes = '';
        $this->interviewQuestions = [];
        $this->practicalExams = [];
    }

    // Calculate total score
    private function calculateTotalScore()
    {
        $interviewTotal = 0;
        $practicalTotal = 0;
        
        foreach ($this->interviewScores as $score) {
            $interviewTotal += floatval($score['score'] ?? 0);
        }
        
        foreach ($this->practicalScores as $score) {
            $practicalTotal += floatval($score['score'] ?? 0);
        }
        
        $maxInterviewScore = count($this->interviewQuestions) * 10;
        $maxPracticalScore = count($this->practicalExams) * 10;
        $maxTotal = $maxInterviewScore + $maxPracticalScore;
        
        if ($maxTotal === 0) return 0;
        
        $percentage = (($interviewTotal + $practicalTotal) / $maxTotal) * 100;
        return round($percentage, 2);
    }

    // Submit interview assessment
    public function submitInterview()
    {
        $candidate = Candidate::find($this->selectedCandidateId);
        if (!$candidate) {
            session()->flash('error', 'Candidate not found.');
            return;
        }

        $totalScore = $this->calculateTotalScore();
        
        // Save interview data
        $candidate->interview_scores = [
            'interview_questions' => $this->interviewScores,
            'practical_exams' => $this->practicalScores,
            'notes' => $this->overallNotes,
        ];
        $candidate->interview_total_score = $totalScore;
        $candidate->status = 'interviewed';
        $candidate->save();

        $this->closeInterviewModal();
        
        // Open result modal to decide pass/fail
        $this->resultCandidate = $candidate;
        $this->showResultModal = true;
    }

    // Mark as passed
    public function markAsPassed()
    {
        if (!$this->resultCandidate) return;

        $this->resultCandidate->interview_result = 'passed';
        $this->resultCandidate->status = 'passed';
        $this->resultCandidate->save();

        // Trigger API to external department for contract preparation
        $this->triggerContractApi($this->resultCandidate);

        session()->flash('message', "Candidate {$this->resultCandidate->candidate_name} has PASSED! Contract preparation initiated.");
        $this->closeResultModal();
    }

    // Mark as failed
    public function markAsFailed()
    {
        if (!$this->resultCandidate) return;

        $this->resultCandidate->interview_result = 'failed';
        $this->resultCandidate->status = 'failed';
        $this->resultCandidate->save();

        session()->flash('message', "Candidate {$this->resultCandidate->candidate_name} has been marked as FAILED.");
        $this->closeResultModal();
    }

    public function closeResultModal()
    {
        $this->showResultModal = false;
        $this->resultCandidate = null;
        $this->interviewResult = '';
    }

    // API call to external department for contract preparation
    private function triggerContractApi($candidate)
    {
        // In production, replace with actual API endpoint
        // This is a placeholder for the contract preparation API
        try {
            // Example API call (commented out for demo)
            // $response = Http::post('https://external-department.com/api/contracts/prepare', [
            //     'candidate_id' => $candidate->id,
            //     'candidate_name' => $candidate->candidate_name,
            //     'candidate_email' => $candidate->candidate_email,
            //     'position' => $candidate->applied_position,
            //     'department' => $candidate->department,
            //     'interview_score' => $candidate->interview_total_score,
            // ]);
            
            // Mark contract as pending
            $candidate->contract_status = 'pending';
            $candidate->save();
            
            logger('Contract API triggered for candidate: ' . $candidate->candidate_name);
        } catch (\Exception $e) {
            logger('Contract API Error: ' . $e->getMessage());
        }
    }

    /**
     * Fetch all assessment data from the HR2 API
     */
    private function fetchAssessmentData()
    {
        try {
            logger('Fetching assessment data from HR2 API...');
            $response = Http::withoutVerifying()->get('https://hr2.jetlougetravels-ph.com/api/assessment');
            
            if ($response->successful()) {
                $data = $response->json();
                
                // Debug: Log the full API response
                logger('=== HR2 Assessment API Response ===');
                logger('Success: ' . ($data['success'] ?? 'N/A'));
                logger('Total items: ' . (isset($data['data']) ? count($data['data']) : 0));
                logger('Full Data: ' . json_encode($data, JSON_PRETTY_PRINT));
                
                $allRoles = collect($data['data'] ?? [])->pluck('role')->unique()->values()->toArray();
                logger('Available roles in API: ' . implode(', ', $allRoles));
                
                return $data['data'] ?? [];
            }
            
            logger('HR2 Assessment API Error: ' . $response->status());
            return [];
        } catch (\Exception $e) {
            logger('HR2 Assessment API Exception: ' . $e->getMessage());
            return [];
        }
    }

    private function getInterviewQuestions($position)
    {
        $assessments = $this->fetchAssessmentData();
        
        // Filter by role (position) and type 'question'
        $questions = collect($assessments)
            ->filter(function ($item) use ($position) {
                $apiRole = trim($item['role'] ?? '');
                $applicantPos = trim($position);
                return (strcasecmp($apiRole, $applicantPos) === 0 || 
                        str_contains(strtolower($applicantPos), strtolower($apiRole)) || 
                        str_contains(strtolower($apiRole), strtolower($applicantPos))) 
                        && $item['type'] === 'question';
            })
            ->pluck('content')
            ->values()
            ->toArray();
        
        // Debug: Log filtered questions
        logger("=== Interview Questions for Position: {$position} ===");
        logger('Questions count: ' . count($questions));
        logger('Questions: ' . json_encode($questions, JSON_PRETTY_PRINT));
        
        return $questions;
    }
    
    private function getPracticalExams($position)
    {
        $assessments = $this->fetchAssessmentData();
        
        // Filter by role (position) and type 'exam'
        $exams = collect($assessments)
            ->filter(function ($item) use ($position) {
                $apiRole = trim($item['role'] ?? '');
                $applicantPos = trim($position);
                return (strcasecmp($apiRole, $applicantPos) === 0 || 
                        str_contains(strtolower($applicantPos), strtolower($apiRole)) || 
                        str_contains(strtolower($apiRole), strtolower($applicantPos))) 
                        && $item['type'] === 'exam';
            })
            ->pluck('content')
            ->values()
            ->toArray();
        
        // Debug: Log filtered exams
        logger("=== Practical Exams for Position: {$position} ===");
        logger('Exams count: ' . count($exams));
        logger('Exams: ' . json_encode($exams, JSON_PRETTY_PRINT));
        
        return $exams;
    }

    public function deleteCandidate($id)
    {
        if (session('user.position') !== 'Super Admin') {
            session()->flash('error', 'Unauthorized action.');
            return;
        }

        $candidate = Candidate::findOrFail($id);
        $candidate->delete();
        session()->flash('message', 'Candidate deleted successfully!');
    }

    public function exportData()
    {
        $export = new \App\Exports\Applicants\InterviewsExport();
        return $export->export();
    }

    public function render()
    {
        // Get candidates ready for interview
        $query = Candidate::query()
            ->latest();
        
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        } else {
            $query->whereIn('status', ['interview_ready', 'interviewed']);
        }
        
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('candidate_name', 'like', "%{$this->search}%")
                ->orWhere('candidate_email', 'like', "%{$this->search}%")
                ->orWhere('applied_position', 'like', "%{$this->search}%");
            });
        }
        
        $candidates = $query->paginate(10);
        $jobs = JobListing::latest()->get();
        
        return view('livewire.user.applicants.interviews', [
            'jobs' => $jobs,
            'candidates' => $candidates,
        ])->layout('layouts.app');
    }
}
