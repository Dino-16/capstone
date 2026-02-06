<?php

namespace App\Livewire\User\Onboarding;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Exports\Onboarding\EmployeesExport;
use App\Models\Onboarding\DocumentChecklist;

class Employees extends Component
{
    use WithPagination;
    use \Livewire\WithFileUploads;

    #[Url]
    public $search = '';

    #[Url]
    public $statusFilter = '';
    
    public $perPage = 10;

    // Modal properties
    public $showEmployeeModal = false;
    public $selectedEmployee = null;
    public $employeeDocuments = [];

    // Edit Modal Properties
    public $showEditModal = false;
    public $editEmployeeId = null;
    public $first_name = '';
    public $last_name = '';
    public $email = '';
    public $phone = '';
    public $position = '';
    public $employment_status = '';

    // Request Contract Modal Properties
    public $showRequestContractModal = false;
    public $requestEmployeeIndex = null;
    public $requestDepartment = '';
    public $requestorName = '';
    public $requestorEmail = '';
    public $requestContractType = '';
    public $requestPurpose = '';

    public $departmentsList = [
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

    /**
     * Resets pagination when search is updated.
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Resets pagination when status filter is updated.
     */
    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    /**
     * Fetches data from the API and returns a Paginator instance.
     */
    protected function getEmployeesPaginator()
    {
        $page = $this->getPage();

        // To support filtering by employment status (which comes from a different API),
        // we need to fetch a sufficient amount of data if we're doing client-side filtering
        // OR fetch all for accurate filtering across pages.
        
        $response = Http::get('http://hr4.jetlougetravels-ph.com/api/employees', [
            'search'   => $this->search,
            'per_page' => 1000, // Fetch more to allow client-side filtering/pagination
        ]);

        if (!$response->successful()) {
            return new LengthAwarePaginator([], 0, $this->perPage, $page);
        }

        $data = $response->json();
        $allEmployees = isset($data['data']['data']) ? $data['data']['data'] : (isset($data['data']) ? $data['data'] : $data);
        
        $enrichedEmployees = $this->enrichEmployeesWithDocumentStatus($allEmployees);
        $collection = collect($enrichedEmployees);

        // Apply Employment Status Filter
        if (!empty($this->statusFilter)) {
            $collection = $collection->filter(function ($emp) {
                return strtolower($emp['employment_status'] ?? '') === strtolower($this->statusFilter);
            });
        }

        // Apply Search Filter (if API didn't handle it or for double safety)
        if (!empty($this->search)) {
            $searchTerm = strtolower(trim($this->search));
            $collection = $collection->filter(function ($emp) use ($searchTerm) {
                $name = strtolower(($emp['first_name'] ?? '') . ' ' . ($emp['last_name'] ?? ''));
                $pos = strtolower($emp['position'] ?? '');
                return str_contains($name, $searchTerm) || str_contains($pos, $searchTerm);
            });
        }

        $total = $collection->count();
        $paginatedItems = $collection->forPage($page, $this->perPage)->values()->toArray();

        return new LengthAwarePaginator(
            $paginatedItems,
            $total,
            $this->perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    /**
     * Enriches employees with their document checklist status
     */
    protected function enrichEmployeesWithDocumentStatus(array $employees): array
    {
        // Get all document checklists for efficient lookup
        $checklists = DocumentChecklist::where('status', 'active')->get();

        // Fetch accounts to get employment status
        $accountStatusMap = [];
        try {
            $accountsResponse = Http::withoutVerifying()->get('https://hr4.jetlougetravels-ph.com/api/accounts');
            if ($accountsResponse->successful()) {
                $accountsData = $accountsResponse->json();
                $systemAccounts = $accountsData['data']['system_accounts'] ?? [];
                $essAccounts = $accountsData['data']['ess_accounts'] ?? [];
                
                foreach (array_merge($systemAccounts, $essAccounts) as $account) {
                    $empId = $account['employee_id'] ?? null;
                    $status = $account['employee']['employee_status'] ?? null;
                    if ($empId && $status) {
                        $accountStatusMap[$empId] = $status;
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error('Failed to fetch accounts for employee status: ' . $e->getMessage());
        }

        return collect($employees)->map(function ($emp) use ($checklists, $accountStatusMap) {
            $employeeName = trim(($emp['first_name'] ?? '') . ' ' . ($emp['last_name'] ?? ''));
            $employeeEmail = $emp['email'] ?? null;
            $employeeId = $emp['id'] ?? null;

            // Find matching document checklist by name or email
            $matchingChecklist = $checklists->first(function ($checklist) use ($employeeName, $employeeEmail) {
                $nameMatch = !empty($employeeName) && 
                    str_contains(strtolower($checklist->employee_name), strtolower($employeeName));
                $emailMatch = !empty($employeeEmail) && 
                    strtolower($checklist->email ?? '') === strtolower($employeeEmail);
                return $nameMatch || $emailMatch;
            });

            // Add document status info to employee
            $emp['has_document_checklist'] = $matchingChecklist !== null;
            $emp['document_checklist_id'] = $matchingChecklist?->id;
            $emp['document_completion'] = $matchingChecklist?->getCompletionPercentage() ?? 0;
            $emp['document_status'] = $matchingChecklist 
                ? ($matchingChecklist->getCompletionPercentage() == 100 ? 'Complete' : 'In Progress') 
                : 'Not Integrated';

            // Add employment status from accounts map or fallback to 'status' field if exists
            $emp['employment_status'] = $accountStatusMap[$employeeId] ?? $emp['status'] ?? '---';

            return $emp;
        })->toArray();
    }

    /**
     * View employee details modal
     */
    public function viewEmployee($index)
    {
        $paginator = $this->getEmployeesPaginator();
        $employees = $paginator->items();
        
        if (isset($employees[$index])) {
            $this->selectedEmployee = $employees[$index];
            
            // Get documents if employee has a checklist
            if ($this->selectedEmployee['has_document_checklist'] ?? false) {
                $checklist = DocumentChecklist::find($this->selectedEmployee['document_checklist_id']);
                $this->employeeDocuments = $checklist?->documents ?? [];
            } else {
                $this->employeeDocuments = [];
            }
            
            $this->showEmployeeModal = true;
        }
    }

    /**
     * Close modal
     */
    public function closeModal()
    {
        $this->showEmployeeModal = false;
        $this->selectedEmployee = null;
        $this->employeeDocuments = [];
    }

    // Request Contract Management
    public function openRequestContractModal($index)
    {
        $paginator = $this->getEmployeesPaginator();
        $employees = $paginator->items();
        
        if (!isset($employees[$index])) return;

        $employee = $employees[$index];
        $employeeName = ($employee['first_name'] ?? '') . ' ' . ($employee['last_name'] ?? '');

        $this->requestEmployeeIndex = $index;
        $this->requestDepartment = session('user.department') ?? $employee['department']['name'] ?? '';
        $this->requestorName = session('user.name') ?? '';
        $this->requestorEmail = session('user.email') ?? '';
        $this->requestContractType = 'Employment Contract';
        $position = $employee['position'] ?? 'N/A';
        $this->requestPurpose = "Requesting contract update/renewal for employee {$employeeName} ({$position}).";
        $this->showRequestContractModal = true;
    }

    public function closeRequestContractModal()
    {
        $this->showRequestContractModal = false;
        $this->requestEmployeeIndex = null;
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

        $paginator = $this->getEmployeesPaginator();
        $employees = $paginator->items();
        $employee = $employees[$this->requestEmployeeIndex] ?? null;

        if (!$employee) return;

        $employeeName = ($employee['first_name'] ?? '') . ' ' . ($employee['last_name'] ?? '');

        try {
            // Trying HTTP instead of HTTPS and switching back to asForm() based on curl findings
            $response = \Illuminate\Support\Facades\Http::withoutVerifying()->asForm()->post('http://legal-admin.jetlougetravels-ph.com/API.php', [
                'action' => 'create_request',
                'requesting_department' => $this->requestDepartment,
                'requestor_name' => $this->requestorName,
                'requestor_email' => $this->requestorEmail,
                'contract_type_requested' => $this->requestContractType,
                'purpose' => $this->requestPurpose,
                'employee_name' => $employeeName,
                'employee_email' => $employee['email'] ?? '',
                'position' => $employee['position'] ?? '',
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

    public function export()
    {
        // For export, we usually want everything matching the search, ignoring pagination
        $response = Http::get('http://hr4.jetlougetravels-ph.com/api/employees', [
            'search'   => $this->search,
            'per_page' => 5000, 
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $items = collect($data['data'] ?? $data); // Handle both paginated/unpaginated API response
            $enrichedItems = $this->enrichEmployeesWithDocumentStatus($items->toArray());

            $exportData = collect($enrichedItems)->map(fn($emp) => [
                'Name'             => ($emp['first_name'] ?? '') . ' ' . ($emp['last_name'] ?? $emp['full_name'] ?? '—'),
                'Position'         => $emp['position'] ?? '—',
                'Department'       => $emp['department']['name'] ?? 'Not Integrated',
                'HR Documents'     => $emp['document_status'] ?? 'Not Integrated',
                'Employment Status' => $emp['employment_status'] ?? '---',
                'Date Hired'       => $emp['date_hired'] ?? '---',
            ]);

            return (new EmployeesExport($exportData->toArray()))->export();
        }

        return response()->streamDownload(fn() => print("Export failed"), "error.xls");
    }

    public function editEmployee($index)
    {
        $paginator = $this->getEmployeesPaginator();
        $employees = $paginator->items();

        if (isset($employees[$index])) {
            $emp = $employees[$index];
            $this->editEmployeeId = $emp['id'] ?? null;
            $this->first_name = $emp['first_name'] ?? '';
            $this->last_name = $emp['last_name'] ?? '';
            $this->email = $emp['email'] ?? '';
            $this->phone = $emp['phone'] ?? $emp['contact_number'] ?? '';
            $this->position = $emp['position'] ?? '';
            $this->employment_status = $emp['employment_status'] ?? $emp['status'] ?? '';

            $this->showEditModal = true;
        }
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->reset(['editEmployeeId', 'first_name', 'last_name', 'email', 'phone', 'position', 'employment_status']);
    }

    public function updateEmployee()
    {
        $this->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
        ]);

        // Placeholder for future API integration
        session()->flash('error', 'Update is not currently supported for external API records.');
        $this->closeEditModal();
    }

    public function deleteEmployee($index)
    {
        // Placeholder for future API integration
        session()->flash('error', 'Deletion is not currently supported for external API records.');
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

            \Log::info('Submitting Contract Approval...', [
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
            
            \Log::info('Contract Approval Response: ' . $response->status() . ' - ' . $response->body());

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

    public function render()
    {
        return view('livewire.user.onboarding.employees', [
            'employees' => $this->getEmployeesPaginator(),
        ])->layout('layouts.app');
    }
}