<?php

namespace App\Livewire\User\Onboarding;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use App\Exports\Onboarding\EmployeesExport;

class Employees extends Component
{
    use WithPagination;

    public $employees = [];
    
    #[Url(keep: true)]
    public $search = '';
    
    public $perPage = 10;

    public function mount()
    {
        $response = Http::get('http://hr4.jetlougetravels-ph.com/api/employees');

        if ($response->successful() && is_array($response->json())) {
            $this->employees = $response->json();
        } else {
            $this->employees = []; 
        }

        // Debug: log what we got
        \Log::info('Employees count: ' . count($this->employees));
        \Log::info('First employee: ' . json_encode($this->employees[0] ?? null));
    }

    public function updatingSearch()
    {
        $this->resetPage();

        // Update employees array when search term changes
        $response = Http::get('http://hr4.jetlougetravels-ph.com/api/employees', [
            'search' => $this->search,
        ]);

        if ($response->successful() && is_array($response->json())) {
            $this->employees = $response->json();
        } else {
            $this->employees = [];
        }
    }

    public function export()
    {
        // Export current filtered employees data to Excel
        $response = Http::get('http://hr4.jetlougetravels-ph.com/api/employees', [
            'search' => $this->search,
            'per_page' => 1000, // Set a high but valid per_page value
        ]);

        if ($response->successful()) {
            $allEmployees = collect($response->json());

            // Filter based on current search
            if (!empty($this->search)) {
                $searchTerm = strtolower(trim($this->search));
                $filteredEmployees = $allEmployees->filter(function ($employee) use ($searchTerm) {
                    $name = strtolower($employee['name'] ?? '');
                    $role = strtolower($employee['role'] ?? '');
                    return str_contains($name, $searchTerm) || str_contains($role, $searchTerm);
                });
            } else {
                $filteredEmployees = $allEmployees;
            }

            // Transform data for export
            $exportData = $filteredEmployees->map(function ($employee) {
                return [
                    'Name' => $employee['name'] ?? '—',
                    'Position' => $employee['role'] ?? '—',
                    'Department' => $employee['department'] ?? 'Not Integrated',
                    'Contract Signing' => 'Completed',
                    'HR Documents' => 'Not Integrated',
                    'Training Modules' => 'Not Integrated',
                ];
            });

            $export = new EmployeesExport($exportData->toArray());
            return $export->export();
        }

        // Return empty response if API fails
        return response()->streamDownload(function () {
            echo "No employee data available to export.";
        }, 'employees_' . date('Y-m-d') . '.xls', [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename=employees_' . date('Y-m-d') . '.xls',
        ]);
    }

    public function render()
    {
        // Fetch paginated data from API (only name and role for search/pagination)
        $page = \Illuminate\Pagination\Paginator::resolveCurrentPage('page');
        $response = Http::get('http://hr4.jetlougetravels-ph.com/api/employees', [
            'search' => $this->search,
            'page' => $page,
            'per_page' => $this->perPage,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $items = $data['data'] ?? [];
            $total = $data['total'] ?? 0;
            $perPage = $data['per_page'] ?? $this->perPage;
            $currentPage = $data['current_page'] ?? $page;

            // If API returns no items (likely doesn't support pagination), fall back to client-side
            if (empty($items)) {
                $collection = collect($this->employees);
                if (!empty($this->search)) {
                    $searchTerm = strtolower(trim($this->search));
                    $filtered = $collection->filter(function ($employee) use ($searchTerm) {
                        $name = strtolower($employee['name'] ?? '');
                        $role = strtolower($employee['role'] ?? '');
                        return str_contains($name, $searchTerm) || str_contains($role, $searchTerm);
                    });
                } else {
                    $filtered = $collection;
                }
                $paginatedItems = $filtered->forPage($page, $this->perPage)->values();
                $employees = new LengthAwarePaginator(
                    $paginatedItems,
                    $filtered->count(),
                    $this->perPage,
                    $page,
                    ['path' => request()->url(), 'query' => request()->query()]
                );
            } else {
                // Merge API items with full employee details
                $mergedItems = collect($items)->map(function ($apiItem) {
                    $match = collect($this->employees)->firstWhere('name', $apiItem['name'] ?? '');
                    return $match ?? $apiItem;
                });

                $employees = new LengthAwarePaginator(
                    $mergedItems,
                    $total,
                    $perPage,
                    $currentPage,
                    ['path' => request()->url(), 'query' => request()->query()]
                );
            }
        } else {
            $employees = new LengthAwarePaginator([], 0, $this->perPage, $page, [
                'path' => request()->url(),
                'query' => request()->query(),
            ]);
        }

        if (!($employees instanceof LengthAwarePaginator)) {
            throw new \Exception('employees is not a paginator: ' . get_class($employees));
        }

        // Debug: log paginator details
        \Log::info('Paginator count: ' . $employees->count());
        \Log::info('Total: ' . $employees->total());
        \Log::info('First item: ' . json_encode($employees->items()[0] ?? null));

        return view('livewire.user.onboarding.employees', [
            'paginator' => $employees,
        ])->layout('layouts.app');
    }
}
