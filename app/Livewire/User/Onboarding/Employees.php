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
     * Fetches data from the API and returns a Paginator instance.
     */
    protected function getEmployeesPaginator()
    {
        $page = $this->getPage(); // Livewire helper for current page

        $response = Http::get('http://hr4.jetlougetravels-ph.com/api/employees', [
            'search'   => $this->search,
            'page'     => $page,
            'per_page' => $this->perPage,
        ]);

        if (!$response->successful()) {
            return new LengthAwarePaginator([], 0, $this->perPage, $page);
        }

        $data = $response->json();

        // Check if API supports pagination natively (returns 'data' and 'total' keys)
        if (isset($data['data'])) {
            $employees = $this->enrichEmployeesWithDocumentStatus($data['data']);
            return new LengthAwarePaginator(
                $employees,
                $data['total'] ?? 0,
                $data['per_page'] ?? $this->perPage,
                $data['current_page'] ?? $page,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        }

        // FALLBACK: API returns a simple array (Manual Client-side Pagination)
        $collection = collect($data);

        if (!empty($this->search)) {
            $searchTerm = strtolower(trim($this->search));
            $collection = $collection->filter(function ($emp) use ($searchTerm) {
                $name = strtolower($emp['first_name'] ?? $emp['full_name'] ?? '');
                $pos = strtolower($emp['position'] ?? '');
                return str_contains($name, $searchTerm) || str_contains($pos, $searchTerm);
            });
        }

        $paginatedItems = $collection->forPage($page, $this->perPage)->values()->toArray();
        $employees = $this->enrichEmployeesWithDocumentStatus($paginatedItems);

        return new LengthAwarePaginator(
            $employees,
            $collection->count(),
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

        return collect($employees)->map(function ($emp) use ($checklists) {
            $employeeName = trim(($emp['first_name'] ?? '') . ' ' . ($emp['last_name'] ?? ''));
            $employeeEmail = $emp['email'] ?? null;

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
                'Employment Status' => $emp['employement_status'] ?? '---',
                'Date Hired'       => $emp['date_hired'] ?? '---',
            ]);

            return (new EmployeesExport($exportData->toArray()))->export();
        }

        return response()->streamDownload(fn() => print("Export failed"), "error.xls");
    }

    public function render()
    {
        return view('livewire.user.onboarding.employees', [
            'employees' => $this->getEmployeesPaginator(),
        ])->layout('layouts.app');
    }
}