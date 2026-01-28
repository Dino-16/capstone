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

    private function getInterviewQuestions($position)
    {
        $questions = [
            'Travel Agent' => [
                'What is your experience in the travel and tourism industry?',
                'How do you handle difficult customer complaints?',
                'What are your favorite travel destinations and why?',
                'How do you stay updated with recent travel regulations and safety protocols?',
                'Can you describe a time when you planned a complex travel itinerary?'
            ],
            'Driver' => [
                'What type of driving license do you hold and which vehicles can you operate?',
                'How do you ensure the safety of passengers and yourself while driving?',
                'Describe your experience with long-distance driving.',
                'How do you handle traffic violations or accidents?',
                'What navigation tools or apps do you use for route planning?'
            ],
            'Procurement Officer' => [
                'What is your experience with procurement and supply chain management?',
                'How do you evaluate and select suppliers for the company?',
                'Describe your experience with budget management and cost reduction.',
                'What procurement software or tools do you use for procurement processes?',
                'How do you ensure compliance with procurement policies and regulations?'
            ],
            'Logistics Staff' => [
                'What is your experience in warehouse operations and inventory management?',
                'How do you ensure timely and accurate order fulfillment?',
                'Describe your experience with logistics software and tracking systems.',
                'How do you handle damaged or lost goods?',
                'What strategies do you use to optimize delivery routes and schedules?'
            ],
            'Financial Staff' => [
                'What accounting software are you proficient in?',
                'Describe your experience with financial reporting and analysis.',
                'How do you ensure the accuracy of financial data entry and calculations?',
                'What is your experience with budget preparation and monitoring?',
                'How do you handle confidential financial information?'
            ],
            'Admin' => [
                'What office management software are you familiar with?',
                'How do you prioritize multiple tasks and deadlines?',
                'Describe your experience with scheduling and calendar management.',
                'How do you handle confidential documents and information?',
                'What is your experience with organizing meetings and events?'
            ]
        ];
        
        return $questions[$position] ?? [];
    }
    
    private function getPracticalExams($position)
    {
        $exams = [
            'Travel Agent' => [
                'Plan a 7-day itinerary for a family of 4 visiting Japan.',
                'Handle a customer complaint about a cancelled flight.',
                'Calculate total cost including flights, hotels, and activities for a Europe tour.',
                'Create a travel package brochure for a beach destination.',
                'Explain visa requirements for different countries.'
            ],
            'Driver' => [
                'Plan the most efficient route for 5 deliveries in the city.',
                'List daily vehicle safety checks before starting work.',
                'Calculate fuel consumption for a 200km trip.',
                'Describe steps to change a flat tire safely.',
                'Explain how to handle a vehicle breakdown situation.'
            ],
            'Procurement Officer' => [
                'Create a purchase request form for office supplies.',
                'Compare 3 supplier quotes and recommend the best option.',
                'Prepare a simple procurement budget for Q1.',
                'Draft an email to request proposals from vendors.',
                'Explain the procurement process from request to delivery.'
            ],
            'Logistics Staff' => [
                'Organize a warehouse layout for maximum efficiency.',
                'Create an inventory tracking sheet for 50 items.',
                'Plan delivery schedule for 20 orders in one day.',
                'Describe how to handle and document damaged goods.',
                'Calculate storage space needed for different product sizes.'
            ],
            'Financial Staff' => [
                'Create a simple monthly expense report.',
                'Reconcile a bank statement with 10 transactions.',
                'Prepare a basic budget spreadsheet for a department.',
                'Calculate VAT and tax for different expense categories.',
                'Create an invoice template for client billing.'
            ],
            'Admin' => [
                'Organize a weekly schedule for 5 team members.',
                'Draft a professional email to announce a company event.',
                'Create a filing system for different document types.',
                'Prepare meeting minutes for a team discussion.',
                'Design a visitor log and sign-in sheet.'
            ]
        ];
        
        return $exams[$position] ?? [];
    }

    public function render()
    {
        // Get candidates ready for interview
        $query = Candidate::query()
            ->whereIn('status', ['interview_ready', 'interviewed'])
            ->latest();
        
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
