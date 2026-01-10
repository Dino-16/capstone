<?php

namespace App\Livewire\User\Applicants;

use Livewire\Component;

class Interviews extends Component
{
    public function render()
    {
        return view('livewire.user.applicants.interviews')->layout('layouts.app');
    }
}
