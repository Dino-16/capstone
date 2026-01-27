<?php

namespace App\Livewire\User\Onboarding;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Exports\Onboarding\EmployeesExport;

class Employees extends Component
{
    use WithPagination;

    #[Url]
    public $search = '';
    
    public $perPage = 10;

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
            return new LengthAwarePaginator(
                $data['data'],
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

        return new LengthAwarePaginator(
            $collection->forPage($page, $this->perPage)->values(),
            $collection->count(),
            $this->perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
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

            $exportData = $items->map(fn($emp) => [
                'Name'             => $emp['first_name'] ?? $emp['full_name'] ?? '—',
                'Position'         => $emp['position'] ?? '—',
                'Department'       => $emp['department']['name'] ?? 'Not Integrated',
                'Contract Signing' => 'Completed',
                'HR Documents'     => 'Not Integrated',
                'Training Modules' => 'Not Integrated',
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