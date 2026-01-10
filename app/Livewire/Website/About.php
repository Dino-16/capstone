<?php

namespace App\Livewire\Website;

use Livewire\Component;

class About extends Component
{
    public function render()
    {
        return view('livewire.website.about')->layout('layouts.website');
    }
}
