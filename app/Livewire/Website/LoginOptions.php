<?php

namespace App\Livewire\Website;

use Livewire\Component;

class LoginOptions extends Component
{
    public function render()
    {
        return view('livewire.website.login-options')->layout('layouts.website');
    }
}
