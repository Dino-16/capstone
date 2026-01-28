<?php

namespace App\Livewire\User\Applicants;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Applicants\Candidate;
use Illuminate\Support\Facades\Mail;

class Candidates extends Component
{
    use WithPagination;

    public $search;
    public $statusFilter = '';

    // View modal properties
    public $showViewModal = false;
    public $selectedCandidate = null;

    // Reschedule modal properties
    public $showRescheduleModal = false;
    public $reschedulingCandidateId = null;
    public $new_interview_date = '';
    public $new_interview_time = '';

    // Send scheduling link modal
    public $showSendLinkModal = false;
    public $sendLinkCandidateId = null;
    public $sendLinkCandidateName = '';
    public $sendLinkCandidateEmail = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    // View candidate details
    public function viewCandidate($candidateId)
    {
        $this->selectedCandidate = Candidate::find($candidateId);
        $this->showViewModal = true;
    }

    public function closeViewModal()
    {
        $this->showViewModal = false;
        $this->selectedCandidate = null;
    }

    // Reschedule interview
    public function openRescheduleModal($candidateId)
    {
        $candidate = Candidate::find($candidateId);
        if (!$candidate) return;

        $this->reschedulingCandidateId = $candidateId;
        if ($candidate->interview_schedule) {
            $this->new_interview_date = $candidate->interview_schedule->format('Y-m-d');
            $this->new_interview_time = $candidate->interview_schedule->format('H:i');
        }
        $this->showRescheduleModal = true;
    }

    public function closeRescheduleModal()
    {
        $this->showRescheduleModal = false;
        $this->reschedulingCandidateId = null;
        $this->new_interview_date = '';
        $this->new_interview_time = '';
    }

    public function rescheduleInterview()
    {
        $this->validate([
            'new_interview_date' => ['required', 'date', 'after_or_equal:today'],
            'new_interview_time' => ['required', 'string'],
        ]);

        $candidate = Candidate::find($this->reschedulingCandidateId);
        if (!$candidate) return;

        $interviewDateTime = $this->new_interview_date . ' ' . $this->new_interview_time;
        $candidate->interview_schedule = $interviewDateTime;
        $candidate->status = 'scheduled';
        $candidate->save();

        session()->flash('message', 'Interview rescheduled successfully!');
        $this->closeRescheduleModal();
    }

    // Send self-scheduling link
    public function openSendLinkModal($candidateId)
    {
        $candidate = Candidate::find($candidateId);
        if (!$candidate) return;

        $this->sendLinkCandidateId = $candidateId;
        $this->sendLinkCandidateName = $candidate->candidate_name;
        $this->sendLinkCandidateEmail = $candidate->candidate_email;
        $this->showSendLinkModal = true;
    }

    public function closeSendLinkModal()
    {
        $this->showSendLinkModal = false;
        $this->sendLinkCandidateId = null;
        $this->sendLinkCandidateName = '';
        $this->sendLinkCandidateEmail = '';
    }

    public function sendSchedulingLink()
    {
        $candidate = Candidate::find($this->sendLinkCandidateId);
        if (!$candidate) return;

        // Generate scheduling token if not exists
        if (!$candidate->scheduling_token) {
            $candidate->generateSchedulingToken();
        }

        // In a real application, you would send an email here
        // For now, we'll just flash a success message with the link
        $schedulingUrl = $candidate->getSchedulingUrl();

        // Simulating email send - in production, use Laravel Mail
        // Mail::to($candidate->candidate_email)->send(new SchedulingLinkMail($candidate, $schedulingUrl));

        session()->flash('message', "Scheduling link sent to {$candidate->candidate_email}. Link: {$schedulingUrl}");
        $this->closeSendLinkModal();
    }

    // Promote to interview (move to interviews step)
    public function promoteToInterview($candidateId)
    {
        $candidate = Candidate::find($candidateId);
        if (!$candidate) return;

        if (!$candidate->interview_schedule) {
            session()->flash('error', 'Please schedule an interview first.');
            return;
        }

        $candidate->status = 'interview_ready';
        $candidate->save();

        session()->flash('message', "Candidate {$candidate->candidate_name} is ready for interview!");
    }

    public function render()
    {
        $query = Candidate::query()
            ->whereIn('status', ['scheduled', 'interview_ready'])
            ->latest();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('candidate_name', 'like', "%{$this->search}%")
                ->orWhere('candidate_email', 'like', "%{$this->search}%")
                ->orWhere('candidate_phone', 'like', "%{$this->search}%")
                ->orWhere('applied_position', 'like', "%{$this->search}%");
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $candidates = $query->paginate(10);

        return view('livewire.user.applicants.candidates', [
            'candidates' => $candidates,
        ])->layout('layouts.app');
    }
}
