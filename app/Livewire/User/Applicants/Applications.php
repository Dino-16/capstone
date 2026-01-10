<?php

namespace App\Livewire\User\Applicants;

use Livewire\Component;

class Applications extends Component
{
    public function render()
    {
        return view('livewire.user.applicants.applications')->layout('layouts.app');
    }
}
