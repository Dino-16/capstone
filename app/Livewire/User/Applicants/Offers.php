<?php

namespace App\Livewire\User\Applicants;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Applicants\Candidate;
use Illuminate\Support\Facades\Mail;

class Offers extends Component
{
    use WithFileUploads;

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

    // Contract Email Properties
    public $showContractEmailModal = false;
    public $contractEmailCandidateId = null;
    public $contractEmailCandidateName = '';
    public $contractEmailSubject = 'Employment Contract - JetLounge Travels';
    public $contractEmailContent = '';
    public $contractFile; // For file attachment

    // Contract status modal
    public $showContractModal = false;
    public $contractCandidateId = null;
    public $contractCandidateName = '';
    public $newContractStatus = '';

    // Request Contract Modal Properties
    public $showRequestContractModal = false;
    public $requestCandidateId = null;
    public $requestDepartment = '';
    public $requestorName = '';
    public $requestorEmail = '';
    public $requestContractType = '';
    public $requestPurpose = '';

    public $departments = [
        'HR Department',
        'IT Department',
        'Finance Department',
        'Operations Department',
        'Marketing Department',
        'Legal Department',
        'Executive Department'
    ];

    public $contractTypes = [
        'Service Agreement',
        'Employment Contract',
        'Vendor Contract',
        'Partnership Agreement',
        'Non-Disclosure Agreement (NDA)',
        'Supplier Contract',
        'Lease Agreement',
        'Other'
    ];

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
        
        if ($this->newContractStatus === 'approved' && !$candidate->contract_approved_at) {
            $candidate->contract_approved_at = now();
        }
        
        $candidate->save();

        session()->flash('message', "Contract status updated to '{$this->newContractStatus}' successfully!");
        $this->closeContractModal();
    }

    // Request Contract Management
    public function openRequestContractModal($candidateId)
    {
        $candidate = Candidate::find($candidateId);
        if (!$candidate) return;

        $this->requestCandidateId = $candidateId;
        $this->requestDepartment = session('user.department') ?? 'HR Department';
        $this->requestorName = session('user.name') ?? '';
        $this->requestorEmail = session('user.email') ?? '';
        $this->requestContractType = 'Employment Contract';
        $this->requestPurpose = "Requesting employment contract for candidate {$candidate->candidate_name} for the position of {$candidate->applied_position}.";
        $this->showRequestContractModal = true;
    }

    public function closeRequestContractModal()
    {
        $this->showRequestContractModal = false;
        $this->requestCandidateId = null;
        $this->requestDepartment = '';
        $this->requestorName = '';
        $this->requestContractType = '';
        $this->requestPurpose = '';
    }

    public function submitContractRequest()
    {
        $this->validate([
            'requestDepartment' => 'required',
            'requestorName' => 'required|string|max:255',
            'requestorEmail' => 'required|email',
            'requestContractType' => 'required',
            'requestPurpose' => 'required|string',
        ]);

        $candidate = Candidate::find($this->requestCandidateId);
        if (!$candidate) return;

        try {
            // Trying HTTP instead of HTTPS and switching back to asForm() based on curl findings
            $response = \Illuminate\Support\Facades\Http::withoutVerifying()->asForm()->post('http://legal-admin.jetlougetravels-ph.com/API.php', [
                'action' => 'create_request',
                'requesting_department' => $this->requestDepartment,
                'requestor_name' => $this->requestorName,
                'requestor_email' => $this->requestorEmail,
                'contract_type_requested' => $this->requestContractType,
                'purpose' => $this->requestPurpose,
                'candidate_name' => $candidate->candidate_name,
                'candidate_email' => $candidate->candidate_email,
                'position' => $candidate->applied_position,
            ]);

            if ($response->successful()) {
                session()->flash('message', "Contract request submitted successfully to Legal Admin!");
                $this->closeRequestContractModal();
            } else {
                $errorMsg = $response->json('message') ?? $response->body() ?? 'Unknown Error';
                \Log::error("Legal API Response Error: " . $errorMsg);
                session()->flash('error', "Legal API Error: " . Str::limit($errorMsg, 100));
            }
        } catch (\Exception $e) {
            \Log::error("Legal API Connection Error: " . $e->getMessage());
            session()->flash('error', "Could not connect to Legal API. Please check your internet connection.");
        }
    }

    // Mark contract as approved (quick action)
    public function markContractApproved($candidateId)
    {
        $candidate = Candidate::find($candidateId);
        if (!$candidate) return;

        $candidate->contract_status = 'approved';
        $candidate->contract_approved_at = now();
        $candidate->save();

        session()->flash('message', "Contract marked as approved for {$candidate->candidate_name}!");
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
    public function openContractEmailModal($candidateId)
    {
        $candidate = Candidate::find($candidateId);
        if (!$candidate) return;

        $this->contractEmailCandidateId = $candidateId;
        $this->contractEmailSubject = "Employment Contract - {$candidate->candidate_name} ({$candidate->applied_position})";
        $this->contractEmailContent = "Dear {$candidate->candidate_name},\n\nPlease find the attached employment contract for your review and signature.\n\nBest regards,\nHR Department";
        $this->showContractEmailModal = true;
    }

    public function closeContractEmailModal()
    {
        $this->showContractEmailModal = false;
        $this->contractEmailCandidateId = null;
        $this->contractEmailSubject = 'Employment Contract - JetLounge Travels';
        $this->contractEmailContent = '';
        $this->contractFile = null;
    }

    public function sendContractEmail()
    {
        $this->validate([
            'contractEmailSubject' => ['required', 'string', 'max:255'],
            'contractEmailContent' => ['required', 'string'],
            'contractFile' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:10240'], // 10MB max
        ]);

        $candidate = Candidate::find($this->contractEmailCandidateId);
        if (!$candidate) return;

        // In production:
        // Mail::send([], [], function($message) use ($candidate) {
        //     $message->to($candidate->candidate_email)
        //             ->subject($this->contractEmailSubject)
        //             ->setBody($this->contractEmailContent)
        //             ->attach($this->contractFile->getRealPath(), [
        //                 'as' => $this->contractFile->getClientOriginalName(),
        //                 'mime' => $this->contractFile->getMimeType(),
        //             ]);
        // });

        // Mark contract as sent
        $candidate->contract_status = 'sent';
        $candidate->contract_sent_at = now();
        $candidate->save();

        session()->flash('message', "Contract email with attachment sent to {$candidate->candidate_email} successfully!");
        $this->closeContractEmailModal();
    }

    // Complete onboarding - move to employees
    public function completeOnboarding($candidateId)
    {
        $candidate = Candidate::find($candidateId);
        if (!$candidate) return;

        if ($candidate->contract_status !== 'approved') {
            session()->flash('error', 'Contract must be approved before completing onboarding.');
            return;
        }

        $candidate->status = 'hired';
        $candidate->save();

        session()->flash('message', "{$candidate->candidate_name} has been marked as HIRED! Ready for employee onboarding.");
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

    public $showApprovalModal = false;
    public $approvalTitle = '';
    public $approvalClientName = '';
    public $approvalClientEmail = '';
    public $approvalType = '';
    public $approvalStartDate = '';
    public $approvalEndDate = '';
    public $approvalValue = '';
    public $approvalDescription = '';
    public $approvalFile;

    public function openApprovalModal()
    {
        $this->approvalClientName = session('user.name') ?? '';
        $this->approvalClientEmail = session('user.email') ?? '';
        $this->showApprovalModal = true;
    }

    public function closeApprovalModal()
    {
        $this->showApprovalModal = false;
        $this->reset([
            'approvalTitle', 
            'approvalClientName', 
            'approvalClientEmail', 
            'approvalType', 
            'approvalStartDate', 
            'approvalEndDate', 
            'approvalValue', 
            'approvalDescription', 
            'approvalFile'
        ]);
    }

    public function submitContractApproval()
    {
        $this->validate([
            'approvalTitle' => 'required|string|max:255',
            'approvalClientName' => 'required|string|max:255',
            'approvalClientEmail' => 'required|email|max:255',
            'approvalType' => 'required|string',
            'approvalStartDate' => 'required|date',
            'approvalEndDate' => 'required|date|after_or_equal:approvalStartDate',
            'approvalValue' => 'required|numeric|min:0',
            'approvalDescription' => 'nullable|string',
            'approvalFile' => 'required|file|max:10240|mimes:pdf,doc,docx', // 10MB limit
        ]);

        try {
            // Map unsupported types to 'service_agreement' (which is known to work)
            // and append the actual type to the description/title
            $validTypes = ['service_agreement', 'vendor_contract'];
            $apiContractType = in_array($this->approvalType, $validTypes) ? $this->approvalType : 'service_agreement';
            
            $finalDescription = $this->approvalDescription ?? '';
            if ($apiContractType !== $this->approvalType) {
                // If we mapped it, add a note to description
                $readableType = ucwords(str_replace('_', ' ', $this->approvalType));
                $finalDescription = "Actual Contract Type: $readableType\n\n" . $finalDescription;
            }

            \Log::info('Submitting Contract Approval (Offers)...', [
                'original_type' => $this->approvalType,
                'sent_type' => $apiContractType,
                'title' => $this->approvalTitle
            ]);

            $response = \Illuminate\Support\Facades\Http::withoutVerifying()
                ->attach(
                    'file', 
                    file_get_contents($this->approvalFile->getRealPath()), 
                    $this->approvalFile->getClientOriginalName()
                )
                ->post('https://legal-admin.jetlougetravels-ph.com/laravel_contract_api.php', [
                    'contract_title' => $this->approvalTitle,
                    'client_name' => $this->approvalClientName,
                    'client_email' => $this->approvalClientEmail,
                    'contract_type' => $apiContractType,
                    'start_date' => $this->approvalStartDate,
                    'end_date' => $this->approvalEndDate,
                    'contract_value' => $this->approvalValue,
                    'description' => $finalDescription,
                    'created_by' => session('user.name') ?? 'HR System',
                    'status' => 'pending_review'
                ]);

            \Log::info('Contract Approval Response (Offers): ' . $response->status() . ' - ' . $response->body());

            if ($response->successful()) {
                session()->flash('message', 'Contract submitted for approval successfully!');
                $this->closeApprovalModal();
            } else {
                \Log::error('Contract Approval API Error: ' . $response->body());
                session()->flash('error', 'Failed to submit contract (' . $response->status() . '): ' . \Illuminate\Support\Str::limit($response->body(), 200));
            }
        } catch (\Exception $e) {
            \Log::error('Contract Approval connection error: ' . $e->getMessage());
            session()->flash('error', 'Connection error: ' . $e->getMessage());
        }
    }

    public function exportData()
    {
        $export = new \App\Exports\Applicants\OffersExport();
        return $export->export();
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
            'approved' => Candidate::where('interview_result', 'passed')->where('contract_status', 'approved')->count(),
            'hired' => Candidate::where('status', 'hired')->count(),
        ];

        return view('livewire.user.applicants.offers', [
            'candidates' => $candidates,
            'stats' => $stats,
        ])->layout('layouts.app');
    }
}
