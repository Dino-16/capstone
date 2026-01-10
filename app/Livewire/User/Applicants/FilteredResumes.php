<?php

namespace App\Livewire\User\Applicants;

use Livewire\Component;

class FilteredResumes extends Component
{
    public function render()
    {
        return view('livewire.user.applicants.filtered-resumes')->layout('layouts.app');
    }
}
