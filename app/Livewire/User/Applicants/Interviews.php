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
    use \App\Livewire\Traits\HandlesToasts;

    public $search;
    public $statusFilter = '';
    public $departmentFilter = '';
    public $positionFilter = '';

    // Message modal properties
    public $showMessageModal = false;
    public $messageSubject = '';
    public $messageBody = '';
    public $selectedCandidateForMessage = null;
    
    // Interview modal properties
    public $showInterviewModal = false;
    public $selectedCandidateId = null;
    public $selectedCandidate = null;
    public $selectedPosition = '';
    
    // Scoring properties
    public $interviewScores = [];
    public $practicalScores = [];
    public $demoScores = []; // Added for demo stage
    public $overallNotes = '';
    
    public $interviewQuestions = [];
    public $practicalExams = [];
    public $demoInstructions = []; // Added for demo stage
    
    // Result properties
    public $showResultModal = false;
    public $resultCandidate = null;
    public $interviewResult = '';
    public $nextStage = null; // To track where to go next

    // Interview Stage definitions
    // Interview Stage definitions
    public const INTERVIEW_STAGES = [
        'initial' => [
            'label' => 'Initial Interview',
            'description' => 'Tell me about yourself.',
            'icon' => 'bi-chat-dots',
            'color' => 'info',
            'next' => 'practical'
        ],
        'practical' => [
            'label' => 'Practical Exam',
            'description' => 'Show me you can do the job.',
            'icon' => 'bi-pencil-square',
            'color' => 'warning',
            'next' => 'demo'
        ],
        'demo' => [
            'label' => 'Demo/Presentation',
            'description' => 'Convince me with a live demonstration.',
            'icon' => 'bi-display',
            'color' => 'success',
            'next' => 'offer'
        ],
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedDepartmentFilter()
    {
        $this->resetPage();
    }

    public function updatedPositionFilter()
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

        // If candidate is already interviewed (submitted but not decided), show Result Modal
        if ($candidate->status === 'interviewed') {
            $this->resultCandidate = $candidate;
            $stage = $candidate->interview_stage ?? 'initial';
            $this->nextStage = self::INTERVIEW_STAGES[$stage]['next'] ?? null;
            $this->showResultModal = true;
            return;
        }

        $this->selectedCandidateId = $candidateId;
        $this->selectedCandidate = $candidate;
        $this->selectedPosition = $candidate->applied_position ?? 'Travel Agent';
        
        // Set initial interview stage if not set
        if (!$candidate->interview_stage) {
            $candidate->interview_stage = 'initial';
            $candidate->save();
        }
        
        $stage = $candidate->interview_stage;

        // Load data based on current stage or load all for reference
        $this->interviewQuestions = $this->getInterviewQuestions($this->selectedPosition);
        $this->practicalExams = $this->getPracticalExams($this->selectedPosition);
        $this->demoInstructions = $this->getDemoInstructions($this->selectedPosition);
        
        // Initialize or Load Scores
        $allScores = $candidate->interview_scores ?? [];
        $existingScores = $allScores[$stage] ?? [];

        if ($stage === 'initial') {
            $this->interviewScores = $existingScores['scores'] ?? array_fill(0, count($this->interviewQuestions), ['answer' => '', 'score' => 1.0]);
        } else {
             $this->interviewScores = array_fill(0, count($this->interviewQuestions), ['answer' => '', 'score' => 1.0]);
        }

        if ($stage === 'practical') {
            $this->practicalScores = $existingScores['scores'] ?? array_fill(0, count($this->practicalExams), ['response' => '', 'score' => 1.0]);
        } else {
            $this->practicalScores = array_fill(0, count($this->practicalExams), ['response' => '', 'score' => 1.0]);
        }

        if ($stage === 'demo') {
            $this->demoScores = $existingScores['scores'] ?? array_fill(0, count($this->demoInstructions), ['notes' => '', 'score' => 1.0]);
        } else {
            $this->demoScores = array_fill(0, count($this->demoInstructions), ['notes' => '', 'score' => 1.0]);
        }
        
        $this->overallNotes = $existingScores['notes'] ?? '';
        
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
        $this->demoScores = [];
        $this->overallNotes = '';
        $this->interviewQuestions = [];
        $this->practicalExams = [];
        $this->demoInstructions = [];
    }

    // Calculate total score
    // Calculate score for current stage
    private function calculateStageScore($stage)
    {
        $total = 0;
        $max = 0;
        $scores = [];
        
        if ($stage === 'initial') {
            $scores = $this->interviewScores;
            $max = count($this->interviewQuestions) * 10;
        } elseif ($stage === 'practical') {
            $scores = $this->practicalScores;
            $max = count($this->practicalExams) * 10;
        } elseif ($stage === 'demo') {
            $scores = $this->demoScores;
            $max = count($this->demoInstructions) * 10;
        }
        
        foreach ($scores as $item) {
            $total += floatval($item['score'] ?? 0);
        }
        
        if ($max === 0) return 0;
        
        $percentage = ($total / $max) * 100;
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

        $stage = $candidate->interview_stage ?? 'initial';
        $score = $this->calculateStageScore($stage);
        
        // Retrieve existing scores or initialize
        $existingScores = $candidate->interview_scores ?? [];
        
        // Update scores for current stage
        $existingScores[$stage] = [
            'scores' => $stage === 'initial' ? $this->interviewScores : ($stage === 'practical' ? $this->practicalScores : $this->demoScores),
            'notes' => $this->overallNotes,
            'stage_score' => $score,
            'timestamp' => now()->toDateTimeString()
        ];
        
        $candidate->interview_scores = $existingScores;
        // Keep status as interviewed internally or partial? 
        // We'll keep it as 'interview_ready' or 'interviewed' but logically we are just at a checkpoint.
        $candidate->status = 'interviewed'; 
        $candidate->interview_total_score = $score; // Save current stage score as the "headline" score for now
        $candidate->save();

        $this->closeInterviewModal();
        
        // Open result modal to decide pass/fail for THIS stage
        $this->resultCandidate = $candidate;
        $this->nextStage = self::INTERVIEW_STAGES[$stage]['next'] ?? null;
        $this->showResultModal = true;
    }

    // Mark as passed
    public function markAsPassed()
    {
        if (!$this->resultCandidate) return;

        $currentStage = $this->resultCandidate->interview_stage;
        $nextStage = self::INTERVIEW_STAGES[$currentStage]['next'] ?? null;

        if ($nextStage && $nextStage !== 'offer') {
            // Proceed to next stage
            $this->resultCandidate->interview_stage = $nextStage;
            $this->resultCandidate->status = 'interview_ready'; // Ready for next stage
            $this->resultCandidate->interview_result = 'passed_' . $currentStage;
            $this->resultCandidate->save();
            
            $nextLabel = self::INTERVIEW_STAGES[$nextStage]['label'];
            $this->toast("Candidate passed {$currentStage} stage! Proceeding to {$nextLabel}.");
        } else {
            // Final stage passed (Demo -> Offer)
            $this->resultCandidate->interview_result = 'passed';
            $this->resultCandidate->status = 'passed';
            $this->resultCandidate->save();

            // Trigger API to external department for contract preparation
            $this->triggerContractApi($this->resultCandidate);
            $this->toast("Candidate {$this->resultCandidate->candidate_name} has PASSED the final interview! Contract preparation initiated.");
        }

        $this->closeResultModal();
    }

    // Mark as failed
    public function markAsFailed()
    {
        if (!$this->resultCandidate) return;

        $currentStage = $this->resultCandidate->interview_stage ?? 'initial';
        $this->resultCandidate->interview_result = 'failed_' . $currentStage;
        $this->resultCandidate->status = 'failed';
        $this->resultCandidate->save();

        $this->toast("Candidate {$this->resultCandidate->candidate_name} has been marked as FAILED.");
        $this->closeResultModal();
    }

    public function closeResultModal()
    {
        $this->showResultModal = false;
        $this->resultCandidate = null;
        $this->interviewResult = '';
        $this->nextStage = null;
    }

    // Messaging functionality
    public function openMessageModal($candidateId)
    {
        $this->selectedCandidateForMessage = Candidate::find($candidateId);
        if ($this->selectedCandidateForMessage) {
            $this->messageSubject = "Update regarding your application - " . ($this->selectedCandidateForMessage->applied_position ?? 'Job Position');
            $this->messageBody = "Dear " . $this->selectedCandidateForMessage->candidate_name . ",\n\n";
            $this->showMessageModal = true;
        }
    }

    public function closeMessageModal()
    {
        $this->showMessageModal = false;
        $this->selectedCandidateForMessage = null;
        $this->messageSubject = '';
        $this->messageBody = '';
    }

    public function sendMessage()
    {
        $this->validate([
            'messageSubject' => 'required|string|max:255',
            'messageBody' => 'required|string',
        ]);

        // In a real application, you would send an actual email here
        // \Illuminate\Support\Facades\Mail::to($this->selectedCandidateForMessage->candidate_email)
        //     ->send(new \App\Mail\CandidateMessage($this->messageSubject, $this->messageBody));

        $this->toast('Email sent successfully to ' . $this->selectedCandidateForMessage->candidate_email);
        $this->closeMessageModal();
    }

    // Update interview stage
    public function updateInterviewStage($candidateId, $stage)
    {
        $candidate = Candidate::find($candidateId);
        if (!$candidate) {
            session()->flash('error', 'Candidate not found.');
            return;
        }

        if (!array_key_exists($stage, self::INTERVIEW_STAGES)) {
            session()->flash('error', 'Invalid interview stage.');
            return;
        }

        $candidate->interview_stage = $stage;
        $candidate->save();

        // Update selected candidate if modal is open
        if ($this->selectedCandidate && $this->selectedCandidate->id === $candidateId) {
            $this->selectedCandidate = $candidate->fresh();
        }

        $stageInfo = self::INTERVIEW_STAGES[$stage];
        $this->toast("Interview stage updated to: {$stageInfo['label']}");
    }

    // Get interview stages for view
    public function getInterviewStages()
    {
        return self::INTERVIEW_STAGES;
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
                $apiRole = strtolower(trim($item['role'] ?? ''));
                $applicantPos = strtolower(trim($position));
                
                // Better fuzzy matching: check if either contains the other, or share common root words
                return ($apiRole === $applicantPos || 
                        str_contains($applicantPos, $apiRole) || 
                        str_contains($apiRole, $applicantPos) ||
                        (str_contains($apiRole, 'logistic') && str_contains($applicantPos, 'logistic')))
                        && $item['type'] === 'question';
            })
            ->pluck('content')
            ->values()
            ->toArray();
        
        // Hardcoded Fallback for common roles if API returns nothing
        if (empty($questions)) {
            $pos = strtolower($position);
            if (str_contains($pos, 'logistic')) {
                $questions = [
                    "Can you describe your experience with inventory management and tracking systems?",
                    "How do you handle unexpected delays or issues in the supply chain?",
                    "What measures do you take to ensure the safety and security of goods during transport and storage?",
                    "Describe a time when you had to optimize a delivery route or logistics process. What was the outcome?"
                ];
            } elseif (str_contains($pos, 'travel agent')) {
                $questions = [
                    "What experience do you have in booking international travel and handling complex itineraries?",
                    "How do you stay updated with the latest travel trends, visa requirements, and destination information?",
                    "Describe a situation where you had to handle a last-minute travel emergency for a client.",
                    "How do you balance meeting sales targets with providing personalized customer service?"
                ];
            }
        }
        
        // Debug: Log filtered questions
        logger("=== Interview Questions for Position: {$position} ===");
        logger('Questions count: ' . count($questions));
        
        return $questions;
    }
    
    private function getPracticalExams($position)
    {
        $assessments = $this->fetchAssessmentData();
        
        // Filter by role (position) and type 'exam'
        $exams = collect($assessments)
            ->filter(function ($item) use ($position) {
                $apiRole = strtolower(trim($item['role'] ?? ''));
                $applicantPos = strtolower(trim($position));
                
                return ($apiRole === $applicantPos || 
                        str_contains($applicantPos, $apiRole) || 
                        str_contains($apiRole, $applicantPos) ||
                        (str_contains($apiRole, 'logistic') && str_contains($applicantPos, 'logistic')))
                        && $item['type'] === 'exam';
            })
            ->pluck('content')
            ->values()
            ->toArray();
        
        // Hardcoded Fallback for common roles if API returns nothing
        if (empty($exams)) {
            $pos = strtolower($position);
            if (str_contains($pos, 'logistic')) {
                $exams = [
                    "Inventory Audit: Given a sample stock list and physical count sheet, identify discrepancies and suggest corrections.",
                    "Route Planning: Use the provided map and delivery list to organize a multi-stop delivery route with different time constraints.",
                    "Documentation Task: Complete a Bill of Lading and a Delivery Receipt based on the provided cargo details."
                ];
            } elseif (str_contains($pos, 'travel agent')) {
                $exams = [
                    "Booking Simulation: Use the mock booking tool to arrange a round-trip flight and hotel stay for a family of four within a specified budget.",
                    "Itinerary Creation: Design a 7-day tour for a group interested in historical sites and local cuisine.",
                    "Scenario Email: Draft a response to a client whose cruise was cancelled, offering three alternative options and explaining the refund policy."
                ];
            }
        }
        
        // Debug: Log filtered exams
        logger("=== Practical Exams for Position: {$position} ===");
        logger('Exams count: ' . count($exams));
        
        return $exams;
    }

    private function getDemoInstructions($position)
    {
        // Generate demo instructions based on position keywords
        // Since we don't have an API for this, we create tailored scenarios
        
        $pos = strtolower($position);
        $tasks = [];

        if (str_contains($pos, 'developer') || str_contains($pos, 'programmer') || str_contains($pos, 'engineer')) {
            $tasks[] = "Live Coding: Create a simple function that processes a list of user data and filters by specific criteria.";
            $tasks[] = "Debug: Identify the bug in this provided code snippet (interviewer to provide snippet).";
            $tasks[] = "Architecture: Draw a high-level diagram for a scalable notification system.";
        } elseif (str_contains($pos, 'designer') || str_contains($pos, 'ui') || str_contains($pos, 'ux')) {
            $tasks[] = "Portfolio Walkthrough: Explain the design decisions behind your best case study.";
            $tasks[] = "Whiteboard Challenge: Redesign the checkout flow of a travel website in 15 minutes.";
            $tasks[] = "Critique: Analyze our current landing page and suggest 3 quick improvements.";
        } elseif (str_contains($pos, 'manager') || str_contains($pos, 'lead') || str_contains($pos, 'director')) {
            $tasks[] = "Strategy: Present a 30-60-90 day plan for this role.";
            $tasks[] = "Conflict Resolution: Roleplay a scenario where two team members are in conflict.";
            $tasks[] = "Resource Planning: How would you handle a 20% budget cut while maintaining team velocity?";
        } elseif (str_contains($pos, 'agent') || str_contains($pos, 'support') || str_contains($pos, 'customer')) {
            $tasks[] = "Mock Call: Handle a furious customer whose flight was cancelled.";
            $tasks[] = "Sales Pitch: Sell a premium travel package to a hesitant budget traveler.";
            $tasks[] = "Tool Proficiency: Demonstrate how you would multitask between chat, email, and booking system.";
        } elseif (str_contains($pos, 'hr') || str_contains($pos, 'recruit')) {
            $tasks[] = "Mock Interview: unexpected candidate behavior handling.";
            $tasks[] = "Policy: Explain a new strict office policy to a resistant employee.";
        } elseif (str_contains($pos, 'logistic') || str_contains($pos, 'warehouse') || str_contains($pos, 'supply')) {
            $tasks[] = "Inventory Simulation: Demonstrate how you would organize a recently arrived bulk shipment of mixed goods.";
            $tasks[] = "Problem Solving: A major delivery is delayed by 4 hours. Walk us through your communication and mitigation plan.";
            $tasks[] = "Safety Audit: Perform a 5-minute mock safety inspection of a designated warehouse area.";
        } else {
            // Generic Default
            $tasks[] = "Role-Specific Presentation: Present a solution to a common problem in your field.";
            $tasks[] = "Scenario Analysis: How would you prioritize multiple urgent tasks?";
            $tasks[] = "Cultural Fit: Describe your ideal work environment and team dynamic.";
        }

        return $tasks;
    }

    public function deleteCandidate($id)
    {
        if (session('user.position') !== 'Super Admin') {
            session()->flash('error', 'Unauthorized action.');
            return;
        }

        $candidate = Candidate::findOrFail($id);
        $candidate->delete();
        $this->toast('Candidate deleted successfully!');
    }

    public function exportData()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\Applicants\InterviewsExport(), 'interviews.xlsx');
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
        
        if ($this->departmentFilter) {
            $query->where('department', $this->departmentFilter);
        }

        if ($this->positionFilter) {
            $query->where('applied_position', $this->positionFilter);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('candidate_name', 'like', "%{$this->search}%")
                ->orWhere('candidate_email', 'like', "%{$this->search}%")
                ->orWhere('applied_position', 'like', "%{$this->search}%");
            });
        }
        
        $candidates = $query->paginate(10);
        
        // Get unique departments and positions for filters
        $filters = [
            'departments' => Candidate::whereNotNull('department')->distinct()->pluck('department'),
            'positions' => Candidate::whereNotNull('applied_position')->distinct()->pluck('applied_position'),
        ];
        
        return view('livewire.user.applicants.interviews', [
            'candidates' => $candidates,
            'interviewStages' => self::INTERVIEW_STAGES,
            'filters' => $filters,
        ])->layout('layouts.app');
    }
}
