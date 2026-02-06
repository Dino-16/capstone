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

    #[Url]
    public $search = '';

    public $perPage = 10;
    public $showModal = false;
    public $showEditModal = false;
    public $showMessageModal = false;
    public $showDrafts = false;
    public $completionFilter = 'All';
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

    // Available document types
    public $documentTypes = [
        'resume' => 'Resume',
        'medical_certificate' => 'Medical Certificate',
        'valid_government_id' => 'Valid Government ID',
        'transcript_of_records' => 'Transcript of Records',
        'nbi_clearance' => 'NBI Clearance',
        'barangay_clearance' => 'Barangay Clearance',
    ];

    public function mount()
    {
        $response = Http::get('http://hr4.jetlougetravels-ph.com/api/employees');

        if ($response->successful()) {
            $json = $response->json();
            // Extract 'data' array from paginated response, or use raw array if not paginated
            $rawEmployees = $json['data'] ?? (is_array($json) ? $json : []);
            
            // Normalize employee names for consistent searching
            $this->employees = collect($rawEmployees)->map(function($emp) {
                $name = $emp['full_name'] ?? trim(($emp['first_name'] ?? '') . ' ' . ($emp['last_name'] ?? ''));
                $emp['name'] = $name;
                $emp['employee_name'] = $name;
                return $emp;
            })->toArray();
            
            $this->filteredEmployees = $this->employees;
        } else {
            $this->employees = [];
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

    public function selectEmployee($id)
    {
        $employee = collect($this->employees)->first(function ($emp) use ($id) {
            return (string)($emp['id'] ?? '') === (string)$id;
        });

        if ($employee) {
            $this->employeeName = $employee['name'] ?? $employee['employee_name'] ?? '';
            $this->email = $employee['email'] ?? null;
        }
        
        $this->showEmployeeDropdown = false;
        $this->filteredEmployees = $this->employees;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedCompletionFilter()
    {
        $this->resetPage();
    }

    public function openModal()
    {
        $this->resetValidation();
        $this->reset(['employeeName', 'email', 'notes', 'selectedDocuments', 'showEmployeeDropdown']);
        $this->selectedDocuments = []; // Start with empty selection
        $this->showModal = true;
    }

    public function openModalForEmployee($name, $email = null)
    {
        $this->resetValidation();
        $this->reset(['notes', 'selectedDocuments', 'showEmployeeDropdown']);
        $this->employeeName = $name;
        $this->email = $email;
        $this->selectedDocuments = []; // Start with empty selection
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
    $this->validate([
        'employeeName' => 'required',
        'email' => 'required|email',
        'selectedDocuments' => 'required|array|min:1'
    ]);

    // Transform flat array into key-value pairs for the JSON/Array column
    $formattedDocuments = [];
    foreach ($this->selectedDocuments as $docKey) {
        $formattedDocuments[$docKey] = 'incomplete'; // Set default status
    }

    DocumentChecklist::create([
        'employee_name' => $this->employeeName,
        'email' => $this->email,
        'documents' => $formattedDocuments,
        'notes' => $this->notes,
        'status' => 'active', // or 'draft'
    ]);

    $this->reset(['showModal', 'employeeName', 'email', 'selectedDocuments', 'notes']);
    $this->dispatch('notify', 'Employee added successfully!'); 
}

    public function updateEmployee()
    {
        try {
            $this->validate([
                'selectedDocuments' => 'required|array|min:1',
            ]);

            $employee = DocumentChecklist::findOrFail($this->editingEmployeeId);
            
            // Update documents with selected ones
            $documents = [];
            foreach ($this->selectedDocuments as $docType) {
                // Preserve existing status if document exists, otherwise set to incomplete
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

    public function toggleDocumentStatus($docType)
    {
        if (!isset($this->documents[$docType])) {
            return;
        }

        // Toggle between complete and incomplete
        $this->documents[$docType] = $this->documents[$docType] === 'complete' ? 'incomplete' : 'complete';
        
        // Save immediately if editing
        if ($this->editingEmployeeId) {
            $employee = DocumentChecklist::findOrFail($this->editingEmployeeId);
            $employee->update(['documents' => $this->documents]);
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
            $employee = DocumentChecklist::findOrFail($employeeId);
            $employee->update(['status' => 'active']);
            
            session()->flash('status', 'Employee restored to active status!');
            
            // Redirect to main view after restore
            $this->showDrafts = false;
            $this->resetPage();
            
            // Force component refresh
            $this->dispatch('refresh-component');
            
        } catch (\Exception $e) {
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

        // Filter by status
        if ($this->showDrafts) {
            $query->where('status', 'draft');
        } else {
            $query->where('status', 'active');
        }

        $documentChecklists = $query->latest()->get();

        // Filter by completion status using the same logic as the view badges
        if ($this->completionFilter !== 'All') {
            if ($this->completionFilter === 'Complete') {
                $documentChecklists = $documentChecklists->filter(function($document) {
                    return $document->getCompletionPercentage() == 100;
                });
            } elseif ($this->completionFilter === 'Incomplete') {
                $documentChecklists = $documentChecklists->filter(function($document) {
                    return $document->getCompletionPercentage() != 100;
                });
            }
        }

        // Get separate drafts data for the drafts table
        $draftsQuery = DocumentChecklist::where('status', 'draft');
        if ($this->search) {
            $draftsQuery->where('employee_name', 'like', '%' . $this->search . '%');
        }
        $drafts = $draftsQuery->latest()->get();

        // Paginate the filtered results
        $documentChecklists = $this->paginateCollection($documentChecklists, $this->perPage);
        $drafts = $this->paginateCollection($drafts, $this->perPage);

        return view('livewire.user.onboarding.document-checklists', [
            'documentChecklists' => $documentChecklists,
            'drafts' => $drafts,
            'documentTypes' => $this->documentTypes,
        ])->layout('layouts.app');
    }

    private function paginateCollection($items, $perPage)
    {
        $currentPage = request()->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        
        $total = $items->count();
        $itemsForCurrentPage = $items->slice($offset, $perPage);
        
        return new \Illuminate\Pagination\LengthAwarePaginator(
            $itemsForCurrentPage,
            $total,
            $perPage,
            $currentPage,
            [
                'path' => request()->url(),
                'pageName' => 'page',
            ]
        );
    }
}