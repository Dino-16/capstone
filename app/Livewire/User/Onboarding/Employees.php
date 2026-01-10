<?php

namespace App\Livewire\User\Onboarding;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

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
    }

    public function export()
    {
        // Export current filtered employees data to Excel
        return response()->streamDownload(function () {
            $page = \Illuminate\Pagination\Paginator::resolveCurrentPage('page');
            $response = Http::get('http://hr4.jetlougetravels-ph.com/api/employees', [
                'search' => $this->search,
                'page' => $page,
                'per_page' => -1, // Get all results for export
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
                
                // Check if there's any data to export
                if ($filteredEmployees->isEmpty()) {
                    return "No employee data found to export.";
                }
                
                // Generate CSV content for Excel
                $csv = "Name,Position,Department,Contract Signing,HR Documents,Training Modules\n";
                
                foreach ($filteredEmployees as $employee) {
                    $csv .= '"' . str_replace('"', '""', $employee['name'] ?? '—') . '",';
                    $csv .= '"' . str_replace('"', '""', $employee['role'] ?? '—') . '",';
                    $csv .= '"' . str_replace('"', '""', $employee['department'] ?? 'Not Integrated') . '",';
                    $csv .= '"' . str_replace('"', '""', 'Completed') . '",';
                    $csv .= '"' . str_replace('"', '""', 'Not Integrated') . '",';
                    $csv .= '"' . str_replace('"', '""', 'Not Integrated') . '"';
                    $csv .= "\n";
                }
                
                return $csv;
            }
        }, 'employees.csv', [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=employees.csv',
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
