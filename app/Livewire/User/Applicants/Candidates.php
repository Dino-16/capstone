<?php

namespace App\Livewire\User\Applicants;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Applicants\Candidate;

class Candidates extends Component
{
    use WithPagination;

    public $search;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Candidate::query()->latest();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('candidate_name', 'like', "%{$this->search}%")
                ->orWhere('candidate_email', 'like', "%{$this->search}%")
                ->orWhere('candidate_phone', 'like', "%{$this->search}%");
            });
        }

        $candidates = $query->paginate(10);

        return view('livewire.user.applicants.candidates', [
            'candidates' => $candidates,
        ])->layout('layouts.app');
    }
}
