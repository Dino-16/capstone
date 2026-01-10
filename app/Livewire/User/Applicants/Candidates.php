<?php

namespace App\Livewire\User\Applicants;

use Livewire\Component;

class Candidates extends Component
{
    public function render()
    {
        return view('livewire.user.applicants.candidates')->layout('layouts.app');
    }
}
