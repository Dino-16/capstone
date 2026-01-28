<?php

namespace App\Livewire\User\Applicants;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Applicants\Candidate;
use Illuminate\Support\Facades\Mail;

class Offers extends Component
{
    use WithPagination;

    public $search;
    public $statusFilter = '';

    // View modal properties
    public $showViewModal = false;
    public $selectedCandidate = null;

    // Email modal properties
    public $showEmailModal = false;
    public $emailCandidateId = null;
    public $emailCandidateName = '';
    public $emailCandidateEmail = '';
    public $emailSubject = 'Required Documents for Your Employment';
    public $emailContent = '';

    // Contract status modal
    public $showContractModal = false;
    public $contractCandidateId = null;
    public $contractCandidateName = '';
    public $newContractStatus = '';

    public function mount()
    {
        // Default email content template
        $this->emailContent = $this->getDefaultEmailTemplate();
    }

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

    // Contract status management
    public function openContractModal($candidateId)
    {
        $candidate = Candidate::find($candidateId);
        if (!$candidate) return;

        $this->contractCandidateId = $candidateId;
        $this->contractCandidateName = $candidate->candidate_name;
        $this->newContractStatus = $candidate->contract_status;
        $this->showContractModal = true;
    }

    public function closeContractModal()
    {
        $this->showContractModal = false;
        $this->contractCandidateId = null;
        $this->contractCandidateName = '';
        $this->newContractStatus = '';
    }

    public function updateContractStatus()
    {
        $candidate = Candidate::find($this->contractCandidateId);
        if (!$candidate) return;

        $candidate->contract_status = $this->newContractStatus;
        
        if ($this->newContractStatus === 'sent' && !$candidate->contract_sent_at) {
            $candidate->contract_sent_at = now();
        }
        
        if ($this->newContractStatus === 'signed' && !$candidate->contract_signed_at) {
            $candidate->contract_signed_at = now();
        }
        
        $candidate->save();

        session()->flash('message', "Contract status updated to '{$this->newContractStatus}' successfully!");
        $this->closeContractModal();
    }

    // Mark contract as signed (quick action)
    public function markContractSigned($candidateId)
    {
        $candidate = Candidate::find($candidateId);
        if (!$candidate) return;

        $candidate->contract_status = 'signed';
        $candidate->contract_signed_at = now();
        $candidate->save();

        session()->flash('message', "Contract marked as signed for {$candidate->candidate_name}!");
    }

    // Email modal for document explainer
    public function openEmailModal($candidateId)
    {
        $candidate = Candidate::find($candidateId);
        if (!$candidate) return;

        $this->emailCandidateId = $candidateId;
        $this->emailCandidateName = $candidate->candidate_name;
        $this->emailCandidateEmail = $candidate->candidate_email;
        $this->emailSubject = "Required Documents for Your Employment - {$candidate->applied_position}";
        $this->emailContent = $this->getDocumentEmailContent($candidate);
        $this->showEmailModal = true;
    }

    public function closeEmailModal()
    {
        $this->showEmailModal = false;
        $this->emailCandidateId = null;
        $this->emailCandidateName = '';
        $this->emailCandidateEmail = '';
        $this->emailSubject = 'Required Documents for Your Employment';
        $this->emailContent = $this->getDefaultEmailTemplate();
    }

    public function sendDocumentEmail()
    {
        $this->validate([
            'emailSubject' => ['required', 'string', 'max:255'],
            'emailContent' => ['required', 'string'],
        ]);

        $candidate = Candidate::find($this->emailCandidateId);
        if (!$candidate) return;

        // In production, use Laravel Mail
        // Mail::to($candidate->candidate_email)->send(new DocumentExplainerMail($this->emailSubject, $this->emailContent));

        // Mark email as sent
        $candidate->documents_email_sent = true;
        $candidate->documents_email_sent_at = now();
        $candidate->save();

        session()->flash('message', "Document explainer email sent to {$this->emailCandidateEmail} successfully!");
        $this->closeEmailModal();
    }

    private function getDefaultEmailTemplate()
    {
        return "Dear Candidate,

Congratulations on your successful interview! We are pleased to proceed with your employment.

Please prepare the following documents and bring them to our office:

REQUIRED DOCUMENTS:
1. Updated Resume/CV
2. Valid Government-issued ID (2 copies)
3. Birth Certificate (NSO/PSA certified)
4. NBI Clearance (within 6 months)
5. Barangay Clearance
6. Police Clearance
7. SSS Number/E1 Form
8. PhilHealth ID/MDR
9. Pag-IBIG MID Number
10. TIN Number
11. 2x2 ID Photos (4 copies, white background)
12. Previous Employment Certificate (if applicable)
13. Transcript of Records/Diploma
14. Medical Certificate (fit to work)
15. Drug Test Result

DEADLINE: Please submit all documents within 5 working days.

If you have any questions, please don't hesitate to contact us.

Best regards,
HR Department";
    }

    private function getDocumentEmailContent($candidate)
    {
        $position = $candidate->applied_position ?? 'the position';
        
        return "Dear {$candidate->candidate_name},

Congratulations on successfully passing your interview for the position of {$position}!

We are excited to have you join our team. Before we finalize your employment, please prepare and submit the following documents:

═══════════════════════════════════════
REQUIRED DOCUMENTS CHECKLIST
═══════════════════════════════════════

PERSONAL IDENTIFICATION:
☐ Updated Resume/CV
☐ Valid Government-issued ID (2 copies)
☐ Birth Certificate (NSO/PSA certified)
☐ Marriage Certificate (if applicable)

CLEARANCES:
☐ NBI Clearance (must be within 6 months)
☐ Barangay Clearance
☐ Police Clearance

GOVERNMENT REGISTRATIONS:
☐ SSS Number/E1 Form
☐ PhilHealth ID/MDR
☐ Pag-IBIG MID Number
☐ TIN Number

EDUCATIONAL/EMPLOYMENT:
☐ Transcript of Records/Diploma (original + photocopy)
☐ Previous Employment Certificate (if applicable)
☐ Certificate of Employment from previous employer

MEDICAL:
☐ Medical Certificate (fit to work)
☐ Drug Test Result (from accredited laboratory)

PHOTOS:
☐ 2x2 ID Photos (4 copies, white background)
☐ 1x1 ID Photos (2 copies, white background)

═══════════════════════════════════════

IMPORTANT REMINDERS:
• Please bring ORIGINAL documents for verification
• Photocopies should be clear and legible
• Deadline: Submit within 5 WORKING DAYS

SUBMISSION LOCATION:
HR Department, Ground Floor
Office Hours: 8:00 AM - 5:00 PM (Monday-Friday)

If you have any questions or concerns, please contact us:
Email: hr@company.com
Phone: (02) 1234-5678

We look forward to welcoming you to the team!

Best regards,
Human Resources Department
JetLounge Travels";
    }

    // Complete onboarding - move to employees
    public function completeOnboarding($candidateId)
    {
        $candidate = Candidate::find($candidateId);
        if (!$candidate) return;

        if ($candidate->contract_status !== 'signed') {
            session()->flash('error', 'Contract must be signed before completing onboarding.');
            return;
        }

        $candidate->status = 'hired';
        $candidate->save();

        session()->flash('message', "{$candidate->candidate_name} has been marked as HIRED! Ready for employee onboarding.");
    }

    public function render()
    {
        // Get candidates who passed interview (in offering stage)
        $query = Candidate::query()
            ->whereIn('status', ['passed', 'hired'])
            ->where('interview_result', 'passed')
            ->latest();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('candidate_name', 'like', "%{$this->search}%")
                ->orWhere('candidate_email', 'like', "%{$this->search}%")
                ->orWhere('applied_position', 'like', "%{$this->search}%");
            });
        }

        if ($this->statusFilter) {
            $query->where('contract_status', $this->statusFilter);
        }

        $candidates = $query->paginate(10);

        // Stats
        $stats = [
            'pending' => Candidate::where('interview_result', 'passed')->where('contract_status', 'pending')->count(),
            'sent' => Candidate::where('interview_result', 'passed')->where('contract_status', 'sent')->count(),
            'signed' => Candidate::where('interview_result', 'passed')->where('contract_status', 'signed')->count(),
            'hired' => Candidate::where('status', 'hired')->count(),
        ];

        return view('livewire.user.applicants.offers', [
            'candidates' => $candidates,
            'stats' => $stats,
        ])->layout('layouts.app');
    }
}
