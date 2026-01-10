<?php

namespace App\Livewire\Website;

use Livewire\Component;

class Contact extends Component
{
    public function render()
    {
        return view('livewire.website.contact')->layout('layouts.website');
    }
}
