<?php

namespace App\Livewire\User\Applicants;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Recruitment\JobListing;
use App\Models\Applicants\Candidate;

class Interviews extends Component
{
    use WithPagination;

    public $candidateName = '';
    public $selectedPosition = '';
    public $currentStep = 1;
    public $interviewAnswers = [];
    public $practicalAnswers = [];
    public $search;

    public function updatedSearch()
    {
        $this->resetPage();
    }
    
    public function render()
    {
        $jobs = JobListing::latest()->get();
        
        // Get candidates with search functionality
        $query = Candidate::query()->latest();
        
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('candidate_name', 'like', "%{$this->search}%")
                ->orWhere('candidate_email', 'like', "%{$this->search}%")
                ->orWhere('candidate_phone', 'like', "%{$this->search}%");
            });
        }
        
        $candidates = $query->paginate(10);
        
        $interviewQuestions = [];
        $practicalExams = [];
        
        if ($this->selectedPosition) {
            $interviewQuestions = $this->getInterviewQuestions($this->selectedPosition);
            $practicalExams = $this->getPracticalExams($this->selectedPosition);
        }
        
        return view('livewire.user.applicants.interviews', [
            'jobs' => $jobs,
            'candidates' => $candidates,
            'interviewQuestions' => $interviewQuestions,
            'practicalExams' => $practicalExams
        ])->layout('layouts.app');
    }
    
    private function getInterviewQuestions($position)
    {
        $questions = [
            'Travel Agent' => [
                'What experience do you have in the travel and tourism industry?',
                'How do you handle difficult customers or complaints?',
                'What destinations are you most familiar with and why?',
                'How do you stay updated with travel regulations and safety protocols?',
                'Describe a time when you had to plan a complex travel itinerary.'
            ],
            'Driver' => [
                'What type of driving license do you hold and what vehicles can you operate?',
                'How do you ensure vehicle safety before and during trips?',
                'Describe your experience with long-distance driving.',
                'How do you handle traffic violations or accidents?',
                'What navigation tools or apps do you use for route planning?'
            ],
            'Procurement Officer' => [
                'What experience do you have with procurement and supply chain management?',
                'How do you evaluate and select suppliers?',
                'Describe your experience with budget management and cost reduction.',
                'What procurement software or tools are you familiar with?',
                'How do you ensure compliance with procurement policies and regulations?'
            ],
            'Logistics Staff' => [
                'What experience do you have in warehouse operations and inventory management?',
                'How do you ensure timely and accurate order fulfillment?',
                'Describe your experience with logistics software and tracking systems.',
                'How do you handle damaged or lost goods?',
                'What strategies do you use to optimize delivery routes and schedules?'
            ],
            'Financial Staff' => [
                'What accounting software are you proficient in?',
                'Describe your experience with financial reporting and analysis.',
                'How do you ensure accuracy in financial data entry and calculations?',
                'What experience do you have with budget preparation and monitoring?',
                'How do you handle confidential financial information?'
            ],
            'Admin' => [
                'What office management software are you familiar with?',
                'How do you prioritize multiple tasks and deadlines?',
                'Describe your experience with scheduling and calendar management.',
                'How do you handle confidential documents and information?',
                'What experience do you have with organizing meetings and events?'
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
    
    public function startInterview()
    {
        if ($this->candidateName && $this->selectedPosition) {
            $this->currentStep = 2;
        }
    }
    
    public function nextStep()
    {
        if ($this->currentStep < 4) {
            $this->currentStep++;
        }
    }
    
    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }
    
    public function submitInterview()
    {
        // Here you would save the interview results to database
        session()->flash('message', 'Interview submitted successfully!');
        $this->reset(['candidateName', 'selectedPosition', 'currentStep', 'interviewAnswers', 'practicalAnswers']);
    }

    public function selectCandidate($candidateId)
    {
        $candidate = Candidate::find($candidateId);
        if ($candidate) {
            $this->candidateName = $candidate->candidate_name;
            // Set position based on candidate's data or default to a position
            // You might need to adjust this based on how your candidate's applied position is stored
            $this->selectedPosition = $candidate->applied_position ?? 'Travel Agent'; // Default fallback
            $this->currentStep = 2; // Move to interview step
        }
    }
}
