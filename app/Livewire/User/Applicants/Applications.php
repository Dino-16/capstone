<?php

namespace App\Livewire\User\Applicants;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Applicants\Application;

class Applications extends Component
{   
    use WithPagination;

    public $search;

    // Pagination Page when Filtered
    public function updatedSearch()
    {
        $this->resetPage();
    }
    
    public function UpdatedStatusFilter()
    {
        $this->resetPage();
    }

    // Clear Message Status
    public function clearStatus()
    {
        session()->forget('status');
    }

    public function render()
    {
        $query = Application::query()->latest();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('first_name', 'like', "%{$this->search}%")
                ->orWhere('last_name', 'like', "%{$this->search}%")
                ->orWhere('email', 'like', "%{$this->search}%")
                ->orWhere('applied_position', 'like', "%{$this->search}%")
                ->orWhere('department', 'like', "%{$this->search}%");
            });
        }

        return view('livewire.user.applicants.applications', [
            'applications' => $query->paginate(10),
        ])->layout('layouts.app');
    }
}
