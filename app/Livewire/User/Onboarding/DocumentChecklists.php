<?php

namespace App\Livewire\User\Onboarding;

use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use App\Models\Onboarding\DocumentChecklist;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class DocumentChecklists extends Component
{
    use WithPagination;

    #[Url(keep: true)]
    public $search = '';

    public $perPage = 10;
    public $showModal = false;
    public $showEditModal = false;
    public $showMessageModal = false;
    public $showDrafts = false;
    public $editingEmployeeId = null;
    public $employees = [];
    public $filteredEmployees = [];
    public $showEmployeeDropdown = false;

    // Form Properties
    public $employeeName;
    public $email;
    public $notes;
    public $selectedDocuments = [];
    public $documents = [];
    
    // Message Properties
    public $messageEmployee;
    public $messageSubject;
    public $messageContent;

    public function mount()
    {
        $response = Http::get('http://hr4.jetlougetravels-ph.com/api/employees');

        if ($response->successful() && is_array($response->json())) {
            $this->employees = $response->json();
            $this->filteredEmployees = $this->employees;
        } else {
            $this->employees = []; // fallback if API fails
            $this->filteredEmployees = [];
        }
    }

    public function updatedEmployeeName($value)
    {
        if (empty($value)) {
            $this->filteredEmployees = $this->employees;
            $this->showEmployeeDropdown = false;
        } else {
            $this->filteredEmployees = collect($this->employees)
                ->filter(function ($employee) use ($value) {
                    $name = strtolower($employee['name'] ?? $employee['employee_name'] ?? '');
                    return strpos($name, strtolower($value)) !== false;
                })
                ->take(10)
                ->values()
                ->toArray();
            $this->showEmployeeDropdown = true;
        }
    }

    public function selectEmployee($employeeName)
    {
        $this->employeeName = $employeeName;
        
        // Find the employee and get their email
        $employee = collect($this->employees)->first(function ($emp) use ($employeeName) {
            $name = $emp['name'] ?? $emp['employee_name'] ?? '';
            return $name === $employeeName;
        });
        
        $this->email = $employee['email'] ?? null;
        
        $this->showEmployeeDropdown = false;
        $this->filteredEmployees = $this->employees;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openModal()
    {
        $this->resetValidation();
        $this->reset(['employeeName', 'email', 'notes', 'selectedDocuments']);
        $this->showModal = true;
    }

    public function editEmployee($employeeId)
    {
        $employee = DocumentChecklist::findOrFail($employeeId);
        $this->editingEmployeeId = $employeeId;
        $this->employeeName = $employee->employee_name;
        $this->email = $employee->email;
        $this->notes = $employee->notes;
        $this->documents = $employee->documents ?? [];
        $this->selectedDocuments = array_keys($this->documents);
        $this->showEditModal = true;
    }

    public function addEmployee()
    {
        try {
            // Debug: Log the incoming data
            logger('Add Employee Data:', [
                'employeeName' => $this->employeeName,
                'email' => $this->email,
                'selectedDocuments' => $this->selectedDocuments,
                'notes' => $this->notes
            ]);

            $this->validate([
                'employeeName' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
                'selectedDocuments' => 'required|array|min:1',
            ]);

            // Initialize documents JSON with selected documents
            $allDocuments = [
                'resume' => 'incomplete',
                'medical_certificate' => 'incomplete',
                'valid_government_id' => 'incomplete',
                'transcript_of_records' => 'incomplete',
                'nbi_clearance' => 'incomplete',
                'barangay_clearance' => 'incomplete',
            ];

            $documents = [];
            foreach ($this->selectedDocuments as $docType) {
                $documents[$docType] = $allDocuments[$docType] ?? 'incomplete';
            }

            logger('Documents to create:', [
                'documents' => $documents,
                'status' => 'active'
            ]);

            DocumentChecklist::create([
                'employee_name' => $this->employeeName,
                'email' => $this->email,
                'documents' => $documents,
                'notes' => $this->notes,
                'status' => 'active',
            ]);

            session()->flash('status', 'Employee added successfully with ' . count($this->selectedDocuments) . ' documents.');
            $this->showModal = false;
            $this->reset(['employeeName', 'email', 'notes', 'selectedDocuments']);
            
        } catch (\Exception $e) {
            logger('Add Employee Error: ' . $e->getMessage());
            session()->flash('error', 'Error adding employee: ' . $e->getMessage());
        }
    }

    public function updateEmployee()
    {
        try {
            $this->validate([
                'selectedDocuments' => 'required|array|min:1',
            ]);

            $employee = DocumentChecklist::findOrFail($this->editingEmployeeId);
            
            // Update documents with selected ones and their statuses
            $documents = [];
            foreach ($this->selectedDocuments as $docType) {
                // Use the toggled status from the form
                $documents[$docType] = $this->documents[$docType] ?? 'incomplete';
            }

            $employee->update([
                'documents' => $documents,
                'email' => $this->email,
                'notes' => $this->notes,
            ]);

            session()->flash('status', 'Employee updated successfully.');
            $this->showEditModal = false;
            $this->reset(['employeeName', 'email', 'notes', 'selectedDocuments', 'editingEmployeeId', 'documents']);
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error updating employee: ' . $e->getMessage());
        }
    }

    public function updatingSelectedDocuments($value, $key)
    {
        // When a document is checked, mark it as complete and save immediately
        if (in_array($value, $this->selectedDocuments)) {
            $this->documents[$value] = 'complete';
        } else {
            // When unchecked, remove from documents
            unset($this->documents[$value]);
        }
        
        // Save immediately to database
        if ($this->editingEmployeeId) {
            $employee = DocumentChecklist::findOrFail($this->editingEmployeeId);
            $employee->update([
                'documents' => $this->documents,
            ]);
        }
    }

    public function deleteEmployee($employeeId)
    {
        DocumentChecklist::findOrFail($employeeId)->delete();
        session()->flash('status', 'Employee deleted successfully.');
    }

    public function draft($employeeId)
    {
        $employee = DocumentChecklist::findOrFail($employeeId);
        $employee->update(['status' => 'draft']);
        session()->flash('status', 'Employee moved to draft status!');
    }

    public function restore($employeeId) 
    {
        try {
            logger('Restore called for employee ID: ' . $employeeId);
            
            $employee = DocumentChecklist::findOrFail($employeeId);
            logger('Employee found:', ['id' => $employee->id, 'name' => $employee->employee_name, 'current_status' => $employee->status]);
            
            $employee->update(['status' => 'active']);
            logger('Employee status updated to active');
            
            session()->flash('status', 'Employee restored to active status!');
            
            // Redirect to main view after restore
            $this->showDrafts = false;
            $this->resetPage();
            
        } catch (\Exception $e) {
            logger('Restore Error: ' . $e->getMessage());
            session()->flash('error', 'Error restoring employee: ' . $e->getMessage());
        }
    }

    public function openDraft()
    {
        $this->showDrafts = true;
        $this->resetPage();
    }

    public function showAll()
    {
        $this->showDrafts = false;
        $this->resetPage();
    }

    public function clearStatus()
    {
        session()->forget(['status', 'error']);
    }

    public function export()
    {
        $export = new \App\Exports\Onboarding\DocumentChecklistsExport();
        return $export->export();
    }

    public function openMessageModal($employeeId)
    {
        $employee = DocumentChecklist::findOrFail($employeeId);
        $this->messageEmployee = $employee;
        $this->messageSubject = '';
        $this->messageContent = '';
        $this->showMessageModal = true;
    }

    public function sendMessage()
    {
        try {
            $this->validate([
                'messageSubject' => 'required|string|max:255',
                'messageContent' => 'required|string|min:10',
            ]);

            // Check if employee has email
            if (!$this->messageEmployee->email) {
                session()->flash('error', 'Employee does not have an email address.');
                return;
            }

            // Send the email
            Mail::raw($this->messageContent, function ($message) {
                $message->to($this->messageEmployee->email)
                    ->subject($this->messageSubject)
                    ->from(config('mail.from.address'), config('mail.from.name'));
            });

            session()->flash('status', 'Message sent to ' . $this->messageEmployee->employee_name . ' at ' . $this->messageEmployee->email);
            $this->showMessageModal = false;
            $this->reset(['messageSubject', 'messageContent', 'messageEmployee']);
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error sending message: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $query = DocumentChecklist::query();

        if ($this->search) {
            $query->where('employee_name', 'like', '%' . $this->search . '%');
        }

        if ($this->showDrafts) {
            // Show only draft employees
            $query->where('status', 'draft');
            $drafts = $query->latest()->paginate($this->perPage);

            return view('livewire.user.onboarding.document-checklists', [
                'documentChecklists' => null,
                'drafts' => $drafts,
            ])->layout('layouts.app');
        }

        // Show incomplete employees (less than 6 documents) but exclude draft status
        $query->where(function($q) {
            $q->whereRaw("JSON_LENGTH(documents) < 6")
              ->orWhereNull('documents');
        })->where('status', '!=', 'draft');
        return view('livewire.user.onboarding.document-checklists', [
            'documentChecklists' => $query->latest()->paginate($this->perPage),
            'drafts' => null,
        ])->layout('layouts.app');
    }
}