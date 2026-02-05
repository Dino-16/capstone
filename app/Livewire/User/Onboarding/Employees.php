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

    #[Url]
    public $search = '';

    #[Url]
    public $statusFilter = '';
    
    public $perPage = 10;

    // Modal properties
    public $showEmployeeModal = false;
    public $selectedEmployee = null;
    public $employeeDocuments = [];

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

    public function deleteEmployee($index)
    {
        // Placeholder for future API integration
        session()->flash('error', 'Deletion is not currently supported for external API records.');
    }

    public function render()
    {
        return view('livewire.user.onboarding.employees', [
            'employees' => $this->getEmployeesPaginator(),
        ])->layout('layouts.app');
    }
}