<?php

namespace App\Livewire\User\Applicants;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Applicants\Candidate;
use Illuminate\Support\Facades\Mail;

class Candidates extends Component
{
    use WithPagination;
    use \App\Livewire\Traits\HandlesToasts;

    public $search;
    public $statusFilter = '';
    public $departmentFilter = '';
    public $positionFilter = '';

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

    // Edit candidate properties
    public $showEditModal = false;
    public $editingCandidateId = null;
    public $candidate_name = '';
    public $candidate_email = '';
    public $candidate_phone = '';
    public $applied_position = '';

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

        $this->toast('Interview rescheduled successfully!');
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

        $this->toast("Scheduling link sent to {$candidate->candidate_email}. Link: {$schedulingUrl}");
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

        $this->toast("Candidate {$candidate->candidate_name} is ready for interview!");
    }

    // Edit candidate
    public function editCandidate($id)
    {
        $candidate = Candidate::findOrFail($id);
        $this->editingCandidateId = $id;
        $this->candidate_name = $candidate->candidate_name;
        $this->candidate_email = $candidate->candidate_email;
        $this->candidate_phone = $candidate->candidate_phone;
        $this->applied_position = $candidate->applied_position;
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->editingCandidateId = null;
        $this->candidate_name = '';
        $this->candidate_email = '';
        $this->candidate_phone = '';
        $this->applied_position = '';
    }

    public function updateCandidate()
    {
        $this->validate([
            'candidate_name' => ['required', 'string', 'max:255'],
            'candidate_email' => ['required', 'email', 'max:255'],
            'candidate_phone' => ['required', 'string', 'max:20'],
            'applied_position' => ['required', 'string', 'max:255'],
        ]);

        $candidate = Candidate::findOrFail($this->editingCandidateId);
        $candidate->update([
            'candidate_name' => $this->candidate_name,
            'candidate_email' => $this->candidate_email,
            'candidate_phone' => $this->candidate_phone,
            'applied_position' => $this->applied_position,
        ]);

        $this->toast('Candidate updated successfully!');
        $this->closeEditModal();
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
        $export = new \App\Exports\Applicants\CandidatesExport();
        return $export->export();
    }

    public function render()
    {
        $query = Candidate::query()
            ->whereIn('status', ['scheduled', 'interview_ready', 'failed'])
            ->latest();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('candidate_name', 'like', "%{$this->search}%")
                ->orWhere('candidate_email', 'like', "%{$this->search}%")
                ->orWhere('candidate_phone', 'like', "%{$this->search}%")
                ->orWhere('applied_position', 'like', "%{$this->search}%")
                ->orWhere('department', 'like', "%{$this->search}%");
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->departmentFilter) {
            $query->where('department', $this->departmentFilter);
        }

        if ($this->positionFilter) {
            $query->where('applied_position', $this->positionFilter);
        }

        $candidates = $query->paginate(10);

        // Get unique departments and positions for filters
        $filters = [
            'departments' => Candidate::whereIn('status', ['scheduled', 'interview_ready', 'failed'])
                ->whereNotNull('department')
                ->distinct()
                ->pluck('department'),
            'positions' => Candidate::whereIn('status', ['scheduled', 'interview_ready', 'failed'])
                ->whereNotNull('applied_position')
                ->distinct()
                ->pluck('applied_position'),
        ];

        return view('livewire.user.applicants.candidates', [
            'candidates' => $candidates,
            'filters' => $filters,
        ])->layout('layouts.app');
    }
}
