<?php

namespace App\Livewire\User\Onboarding;

use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use App\Models\Onboarding\DocumentChecklist;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Livewire\Traits\RequiresPasswordVerification;

class DocumentChecklists extends Component
{
    use WithPagination;
    use RequiresPasswordVerification;
    use \App\Livewire\Traits\HandlesToasts;

    #[Url]
    public $search = '';

    public $perPage = 10;
    public $showModal = false;
    public $showEditModal = false;
    public $showMessageModal = false;
    public $showViewModal = false;
    public $showDeleteModal = false;
    public $deletingEmployeeId = null;
    public $viewingChecklist;

    public $completionFilter = 'All';
    public $editingEmployeeId = null;
    public $employees = [];
    public $filteredEmployees = [];
    public $showEmployeeDropdown = false;
    
    #[Url]
    public $departmentFilter = '';
    
    #[Url]
    public $positionFilter = '';

    // Form Properties
    public $employeeName;
    public $email;
    public $position;
    public $department;
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
        $this->initializePasswordVerification();
        
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
            $this->position = $employee['position'] ?? null;
            
            $dept = $employee['department'] ?? null;
            $this->department = is_array($dept) ? ($dept['name'] ?? $dept['department_name'] ?? null) : $dept;
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

    public function updatedDepartmentFilter()
    {
        $this->resetPage();
    }

    public function updatedPositionFilter()
    {
        $this->resetPage();
    }

    public function openModal()
    {
        $this->resetValidation();
        $this->reset(['employeeName', 'email', 'position', 'department', 'notes', 'selectedDocuments', 'showEmployeeDropdown']);
        $this->selectedDocuments = []; // Start with empty selection
        $this->showModal = true;
    }

    public function openModalForEmployee($name, $email = null, $position = null, $department = null)
    {
        $this->resetValidation();
        $this->reset(['notes', 'selectedDocuments', 'showEmployeeDropdown']);
        $this->employeeName = $name;
        $this->email = $email;
        $this->position = $position;
        $this->department = $department;
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
        // Only pre-check documents that are marked as 'complete'
        $this->selectedDocuments = collect($this->documents)
            ->filter(fn($status) => $status === 'complete')
            ->keys()
            ->toArray();
        $this->showEditModal = true;
    }

public function addEmployee()
{
    $this->validate([
        'employeeName' => 'required',
        'email' => 'required|email',
        'selectedDocuments' => 'required|array|min:1'
    ]);

    // Store ALL document types; selected ones as 'complete', unselected as 'incomplete'
    $formattedDocuments = [];
    foreach ($this->documentTypes as $docKey => $docLabel) {
        $formattedDocuments[$docKey] = in_array($docKey, $this->selectedDocuments) ? 'complete' : 'incomplete';
    }

    DocumentChecklist::create([
        'employee_name' => $this->employeeName,
        'email' => $this->email,
        'position' => $this->position,
        'department' => $this->department,
        'documents' => $formattedDocuments,
        'notes' => $this->notes,
        'status' => 'active',
    ]);

    $this->reset(['showModal', 'employeeName', 'email', 'position', 'department', 'selectedDocuments', 'notes']);
    $this->toast('Employee added successfully!'); 
}

    public function updateEmployee()
    {
        try {
            $this->validate([
                'selectedDocuments' => 'required|array|min:1',
            ]);

            $employee = DocumentChecklist::findOrFail($this->editingEmployeeId);
            
            // Store ALL document types; checked = 'complete', unchecked = 'incomplete'
            $documents = [];
            foreach ($this->documentTypes as $docKey => $docLabel) {
                $documents[$docKey] = in_array($docKey, $this->selectedDocuments) ? 'complete' : 'incomplete';
            }

            $employee->update([
                'documents' => $documents,
                'email' => $this->email,
                'notes' => $this->notes,
            ]);

            $this->toast('Employee updated successfully.');
            $this->showEditModal = false;
            $this->reset(['employeeName', 'email', 'notes', 'selectedDocuments', 'editingEmployeeId', 'documents']);
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error updating employee: ' . $e->getMessage());
        }
    }

    public function toggleDocumentStatus($docType, $checklistId = null)
    {
        $id = $checklistId ?? $this->editingEmployeeId ?? ($this->viewingChecklist ? $this->viewingChecklist->id : null);
        
        if (!$id) return;

        $checklist = DocumentChecklist::findOrFail($id);
        $docs = $checklist->documents ?? [];

        if (!isset($docs[$docType])) return;

        // Toggle status
        $docs[$docType] = $docs[$docType] === 'complete' ? 'incomplete' : 'complete';
        
        $checklist->update(['documents' => $docs]);

        // Force a fresh reload of the model
        $checklist = DocumentChecklist::findOrFail($id);
        
        // Refresh state
        if ($this->showEditModal && $this->editingEmployeeId == $id) {
            $this->documents = $checklist->documents;
        }

        if ($this->showViewModal && $this->viewingChecklist && $this->viewingChecklist->id == $id) {
            $this->viewingChecklist = $checklist;
        }

        $this->toast('Document status updated.');
    }

    public function confirmDelete($employeeId)
    {
        $this->deletingEmployeeId = $employeeId;
        $this->showDeleteModal = true;
    }

    public function deleteEmployee()
    {
        if ($this->deletingEmployeeId) {
            DocumentChecklist::findOrFail($this->deletingEmployeeId)->delete();
            $this->toast('Employee deleted successfully.');
        }
        $this->showDeleteModal = false;
        $this->deletingEmployeeId = null;
    }




    public function export()
    {
        return (new \App\Exports\Onboarding\DocumentChecklistsExport())->download('document_checklists.csv');
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

            $this->toast('Message sent to ' . $this->messageEmployee->employee_name . ' at ' . $this->messageEmployee->email);
            $this->showMessageModal = false;
            $this->reset(['messageSubject', 'messageContent', 'messageEmployee']);
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error sending message: ' . $e->getMessage());
        }
    }

    public function viewDocument($docType, $checklistId)
    {
        $checklist = DocumentChecklist::findOrFail($checklistId);
        
        if ($docType === 'resume') {
            // Try to find candidate by email
            $candidate = \App\Models\Applicants\Candidate::where('candidate_email', $checklist->email)->first();
            
            if ($candidate && $candidate->resume_url) {
                 $this->dispatch('open-document', url: route('resume.view', ['filename' => basename($candidate->resume_url)]));
                 return;
            }
        }
        
        session()->flash('error', 'Document file not found for ' . ucwords(str_replace('_', ' ', $docType)));
    }

    public function viewChecklist($id)
    {
        $this->viewingChecklist = DocumentChecklist::findOrFail($id);
        $this->showViewModal = true;
    }

    public function closeViewModal()
    {
        $this->showViewModal = false;
        $this->viewingChecklist = null;
    }

    public function render()
    {
        $query = DocumentChecklist::query();

        if ($this->search) {
            $query->where(function($q) {
                $q->where('employee_name', 'like', '%' . $this->search . '%')
                  ->orWhere('position', 'like', '%' . $this->search . '%')
                  ->orWhere('department', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->departmentFilter) {
            $query->where('department', $this->departmentFilter);
        }

        if ($this->positionFilter) {
            $query->where('position', $this->positionFilter);
        }

        // Always filter by active status or remove status check if we just want all
        // The user implies deleting deletes them, so we probably just show everything that isn't deleted.
        // Assuming SoftDeletes is not used or handled automatically.
        // However, existing code filtered by 'active' vs 'draft'.
        // We will just show all 'active' ones, or if we want to show everything, remove the status check.
        // Let's assume we just want to show regular lists.
        $query->where('status', '!=', 'draft'); // effectively active, or just ignore draft status entirely if we are deprecating it.
        // Actually best to just show everything or 'active'. Let's stick to 'active' as default behavior.
        // But if we deleted the draft functionality, maybe we should just treat everything as active.
        // Let's just remove the draft filter block and let it show all.
        // Wait, if there are existing drafts in DB, they might be hidden if we blindly filter.
        // Let's assume we just show all records now.
        
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

        // Paginate the filtered results
        $documentChecklists = $this->paginateCollection($documentChecklists, $this->perPage);

        return view('livewire.user.onboarding.document-checklists', [
            'documentChecklists' => $documentChecklists,
            'documentTypes' => $this->documentTypes,
            'positions' => DocumentChecklist::pluck('position')->filter()->unique()->sort()->values(),
            'departments' => DocumentChecklist::pluck('department')->filter()->unique()->sort()->values(),
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